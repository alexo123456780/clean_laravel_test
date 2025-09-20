<?php

namespace App\Domain\ValueObjects;

class Email{


    private string $value;

    public function __construct(string $email)
    {
        $this->value = $this->validateEmail($email);
        
    }


    public function getValue(): string{

        return $this->value;

    }


    public function getDomain(): string{

        return substr($this->value, strpos($this->value,'@')+ 1);
    }

    public function getLocalPart(): string{
        $atPosition = strpos($this->value, '@');
        
        if ($atPosition === false) {
            throw new \InvalidArgumentException('Email inválido: no contiene @');
        }

        return substr($this->value, 0, $atPosition);
    }


    public function equals(Email $other): bool{

        return $this->value === $other->value;


    }


    private function validateEmail(string $email):string{

        $email = trim(strtolower($email));

        if(empty($email)){

            throw new \InvalidArgumentException('El email es un campo obligatorio');
        }

        //el filter_var es para filtrar algo que cumple el filtrado de tipo de dato

        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){

            throw new \InvalidArgumentException('Email no valido');
            
        }

        if(strlen($email) > 255 ){

            throw new \InvalidArgumentException('El email no puede pasar mas de 255 caracteres');
        }


        $bloquedDomains = ['tempmail.com', '10minuteemail.com', 'guerrillaamail.com'];

        $domain = $this->extractDomain($email);

        if(in_array($domain, $bloquedDomains)){

            throw new \InvalidArgumentException('Este email no es valido');
        }


        return $email;

    }



    private function extractDomain(string $email): string{

        return substr($email,strpos($email,'@')+ 1);


    }



    public function __toString(): string
    {

        return $this->value;
        
    }





}