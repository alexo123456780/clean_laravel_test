<?php

namespace App\Domain\ValueObjects;

class Password {

    private string $hash;
    private ?string $plainText;

    private function __construct(string $hash, ?string $plainText = null)
    {
        $this->hash = $hash;
        $this->plainText = $plainText;
        
    }

    public static function fromPlainText(string $password): self{

        $validatePlain = self::validatePassword($password);

        $hash = password_hash($validatePlain,PASSWORD_ARGON2I,[

            'memory_cost' => 65536,
            'time_cost' => 4,
            'threads' => 3
        ]);

        return new self($hash,$validatePlain);
    }

    public static function fromHash(string $hash): self{
        if (empty($hash)) {
            throw new \InvalidArgumentException('El hash no puede estar vacío');
        }

        return new self($hash);
    }




    public function getPlainText(): ?string
    {
        return $this->plainText;
    }

    public function getHash(): string
    {
        return $this->hash;
    }

    public function verify(string $plainPassword): bool{
        return password_verify($plainPassword, $this->hash);
    }

    public function equals(Password $other): bool{

        return $this->hash === $other->hash;

    }



    private static function validatePassword(string $password):string{

        if(empty($password)){

            throw new \InvalidArgumentException('El password no debe estar vacio');

        }


        if(strlen($password) < 8  ){

            throw new \InvalidArgumentException('El password no debe ser menor a 8 caracteres');
        }

        if(strlen($password) > 20 ){


            throw new \InvalidArgumentException('El password no debe ser mayor a 20 caracteres');

        }

        return $password;

    }









}