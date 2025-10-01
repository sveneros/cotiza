<?php 

class Security { 
    public static function encrypt($texto) {               
        $ciphering = "AES-128-CTR";
        $iv_length = openssl_cipher_iv_length($ciphering);
        $options = 0;
        $encryption_iv = '1234567891011121';
        $encryption_key = "SelenitaVenerosMamani";
        $encrypted = openssl_encrypt($texto, $ciphering,$encryption_key, $options, $encryption_iv);  
        return $encrypted;
    }
    public static function decrypt($encryption) {
        $ciphering = "AES-128-CTR";
        $options = 0;
        $decryption_iv = '1234567891011121';
        $decryption_key = "SelenitaVenerosMamani";
        $decryption=openssl_decrypt ($encryption, $ciphering,$decryption_key, $options, $decryption_iv);
        return $decryption;
    }
            
}
?>