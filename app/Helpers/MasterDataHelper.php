<?php

namespace App\Helpers;

class MasterDataHelper
{
    /**
     * Master List of Official Partner Banks
     */
    public static function getBanks(): array
    {
        return [
            'Bank Mandiri' => 'Bank Mandiri (Persero)',
            'BCA' => 'Bank Central Asia (BCA)',
            'BNI' => 'Bank Negara Indonesia (BNI)',
            'BRI' => 'Bank Rakyat Indonesia (BRI)',
            'BSI' => 'Bank Syariah Indonesia (BSI)',
            'BTN' => 'Bank Tabungan Negara (BTN)',
            'Bank Jatim' => 'Bank Pembangunan Daerah Jawa Timur (Bank Jatim)',
            'CIMB Niaga' => 'Bank CIMB Niaga',
            'Bank Permata' => 'Bank Permata',
        ];
    }

    /**
     * Master List of Religions
     */
    public static function getReligions(): array
    {
        return [
            'Islam' => 'Islam',
            'Kristen Protestan' => 'Kristen Protestan',
            'Katolik' => 'Katolik',
            'Hindu' => 'Hindu',
            'Buddha' => 'Buddha',
            'Khonghucu' => 'Khonghucu',
        ];
    }

    /**
     * Master List of Top Partner Universities / Institutions
     */
    public static function getInstitutions(): array
    {
        return [
            'Institut Teknologi Sepuluh Nopember (ITS)',
            'Universitas Airlangga (UNAIR)',
            'Universitas Gadjah Mada (UGM)',
            'Politeknik Elektronika Negeri Surabaya (PENS)',
            'Universitas Brawijaya (UB)',
            'Universitas Indonesia (UI)',
            'Institut Teknologi Bandung (ITB)',
            'Politeknik Perkapalan Negeri Surabaya (PPNS)',
            'Universitas Pembangunan Nasional "Veteran" Jawa Timur',
            'Universitas Negeri Surabaya (UNESA)',
        ];
    }

    /**
     * Master List of Genders
     */
    public static function getGenders(): array
    {
        return [
            'L' => 'Laki-laki',
            'P' => 'Perempuan',
        ];
    }
}
