<?php

namespace HabibAlkhabbaz\IdentityDocuments\Services;

use Google\Cloud\Vision\V1\ImageAnnotatorClient;
use HabibAlkhabbaz\IdentityDocuments\IdentityImage;
use HabibAlkhabbaz\IdentityDocuments\Interfaces\FaceDetection;
use HabibAlkhabbaz\IdentityDocuments\Interfaces\Ocr;
use HabibAlkhabbaz\IdentityDocuments\Responses\OcrResponse;
use Intervention\Image\Image;

class Google implements FaceDetection, Ocr
{
    private ImageAnnotatorClient $annotator;

    private array $credentials;

    public function __construct()
    {
        $this->credentials = config('google_key');
        $this->annotator = new ImageAnnotatorClient(['credentials' => $this->credentials]);
    }

    public function ocr(Image $image): OcrResponse
    {
        $response = $this->annotator->textDetection((string) $image->encode());
        $text = $response->getTextAnnotations();

        return new OcrResponse($text[0]->getDescription());
    }

    public function detect(IdentityImage $image): ?Image
    {
        $response = $this->annotator->faceDetection((string) $image->image->encode());

        $largest = 0;
        $largestFace = null;

        foreach ($response->getFaceAnnotations() as $face) {
            $dimensions = $this->getFaceDimensions($face);
            if ($largest < $dimensions['width'] + $dimensions['height']) {
                $largest = $dimensions['width'] + $dimensions['height'];
                $largestFace = $dimensions;
            }
        }

        if (! $largestFace) {
            return null;
        }

        $face = $image->image;
        $face->resizeCanvas($largestFace['centerX'] * 2, $largestFace['centerY'] * 2, 'top-left');
        $face->rotate($largestFace['roll']);
        $face->resizeCanvas($largestFace['width'], $largestFace['height'], 'center');

        return $face;
    }

    private function getFaceDimensions($face): array
    {
        $rectangle = [];
        $roll = $face->getRollAngle();

        foreach ($face->getBoundingPoly()->getVertices() as $key => $vertex) {
            $rectangle[$key] = [];
            $rectangle[$key]['x'] = $vertex->getX();
            $rectangle[$key]['y'] = $vertex->getY();
        }

        $rectangle['width'] = $rectangle[1]['x'] - $rectangle[0]['x'];
        $rectangle['height'] = $rectangle[3]['y'] - $rectangle[0]['y'];
        $rectangle['centerX'] = $rectangle[0]['x'] + $rectangle['width'] / 2;
        $rectangle['centerY'] = $rectangle[0]['y'] + $rectangle['height'] / 2;
        $rectangle['roll'] = $roll;

        return $rectangle;
    }
}
