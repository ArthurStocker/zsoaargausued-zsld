<?php
$password = randomPassword();
echo 'Password      >' . $password . "<\n";
echo 'Password Hash >' . password_hash($password,  PASSWORD_DEFAULT) . "<\n";

function randomPassword() {
    $pass = array(); //remember to declare $pass as an array
    $alphaLength = 7;
    $d = rand(0, $alphaLength);
    do {
        $s = rand(0, $alphaLength);
    } while ( $s === $d );
    for ($i = 0; $i < 8; $i++) {
        if ( $i === $d ) {
            $pass[] = randomNumber();
        } else if ( $i === $s ) {
            $pass[] = randomSpecChar(); 
        } else {
            if ( rand(0, 1) === 1 )  {
                $pass[] = randomUpper();
            } else {
                $pass[] = randomLower();
            }

        }
    }
    return implode($pass); //turn the array into a string
}

function randomUpper() {
    $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $pass = array(); //remember to declare $pass as an array
    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
    $n = rand(0, $alphaLength);
    return $alphabet[$n]; //turn the array into a string
}
function randomLower() {
    $alphabet = 'abcdefghijklmnopqrstuvwxyz';
    $pass = array(); //remember to declare $pass as an array
    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
    $n = rand(0, $alphaLength);
    return $alphabet[$n]; //turn the array into a string
}

function randomNumber() {
    $alphabet = '1234567890';
    $pass = array(); //remember to declare $pass as an array
    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
    $n = rand(0, $alphaLength);
    return $alphabet[$n]; //turn the array into a string
}

function randomSpecChar() {
    $alphabet = '+*%&$?';
    $pass = array(); //remember to declare $pass as an array
    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
    $n = rand(0, $alphaLength);
    return $alphabet[$n]; //turn the array into a string
}
?>