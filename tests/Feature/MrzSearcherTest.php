<?php

namespace HabibAlkhabbaz\IdentityDocuments\Tests\Feature;

use HabibAlkhabbaz\IdentityDocuments\Mrz\MrzSearcher;
use HabibAlkhabbaz\IdentityDocuments\Tests\TestCase;

class MrzSearcherTest extends TestCase
{
    public function test_correct_td3_mrz_is_found_within_text()
    {
        $text = 'PASPOORT PASSPORT PASSEPORT O KONINKRIJK DER NEDERLANDEN KINGDOM OF THE NETHERLANDS ROYAUME DES PAYSBAS P NLD Nederlandse SPECI2014 De Bruijn e/v Molenaar Willeke Liselotte 10 MAA/MAR 1965  Specimen V/F 1,75 m dm ve wwwd 15 JAN/JAN 2014 15 JAN/JAN 2024 1935 Burg. van Stad en Dorp w.L. de 3ujn P<NLDDE<BRUIJN<<WILLEKE<LISELOTTE<<<<<<<<<<< SPECI20142NLD6503101F2401151999999990<<<<<82';

        $searcher = new MrzSearcher();

        $this->assertEquals(
            'P<NLDDE<BRUIJN<<WILLEKE<LISELOTTE<<<<<<<<<<<SPECI20142NLD6503101F2401151999999990<<<<<82',
            $searcher->search($text)
        );
    }

    public function test_correct_td3_mrz_is_found_without_text()
    {
        $text = 'P<NLDDE<BRUIJN<<WILLEKE<LISELOTTE<<<<<<<<<<< SPECI20142NLD6503101F2401151999999990<<<<<82';

        $searcher = new MrzSearcher();

        $this->assertEquals(
            'P<NLDDE<BRUIJN<<WILLEKE<LISELOTTE<<<<<<<<<<<SPECI20142NLD6503101F2401151999999990<<<<<82',
            $searcher->search($text)
        );
    }

    public function test_malformed_mrz_is_not_found_in_text()
    {
        $text = 'PASPOORT PASSPORT PASSEPORT O KONINKRIJK DER NEDERLANDEN KINGDOM OF THE NETHERLANDS ROYAUME DES PAYSBAS P NLD Nederlandse SPECI2014 De Bruijn e/v Molenaar Willeke Liselotte 10 MAA/MAR 1965  Specimen V/F 1,75 m dm ve wwwd 15 JAN/JAN 2014 15 JAN/JAN 2024 1935 Burg. van Stad en Dorp w.L. de 3ujn P<NLDDE<BRUIJN<<WILLEKE<LISELOTTE<<<<<<<<<<< SPECI20142NLD4503101F2401151999999990<<<<<82';

        $searcher = new MrzSearcher();
        $this->assertEquals(null, $searcher->search($text));
    }

    public function test_correct_mrva_mrz_is_found_without_text()
    {
        $text = "V<UTOERIKSSON<<ANNA<MARIA<<<<<<<<<<<<<<<<<<<\n".
            'L8988901C4XXX4009078F96121096ZE184226B<<<<<<';

        $searcher = new MrzSearcher();

        $this->assertEquals(
            'V<UTOERIKSSON<<ANNA<MARIA<<<<<<<<<<<<<<<<<<<L8988901C4XXX4009078F96121096ZE184226B<<<<<<',
            $searcher->search($text)
        );
    }

    public function test_correct_mrvb_mrz_is_found_without_text()
    {
        $text = "V<UTOERIKSSON<<ANNA<MARIA<<<<<<<<<<<\n".
            'L8988901C4XXX4009078F9612109<<<<<<<<';

        $searcher = new MrzSearcher();

        $this->assertEquals(
            'V<UTOERIKSSON<<ANNA<MARIA<<<<<<<<<<<L8988901C4XXX4009078F9612109<<<<<<<<',
            $searcher->search($text)
        );
    }
}
