<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Attribute as ODM;
use Symfony\Component\Validator\Constraints as Assert;

#[ODM\Document(collection: "products")]
class Product {
    
    #[ODM\Id]
    private ?string $id = null;

    #[ODM\Field(type: 'string')]
    #[Assert\NotBlank(message: 'Title is required')]
    private string $title = '';

    #[ODM\Field(type: 'float')]
    #[Assert\Positive(message: 'Price must be positive')]
    private float $price = 0.0;
    
    #[ODM\Field(type: 'string')]
    #[Assert\Choice(['ali', 'local'])]
    private string $source = "local";

    #[ODM\Field(type: 'string')]
    #[Assert\NotBlank(message: 'Please enter supplier sku')]
    private string $supplierSku = '';

    public function getId() : ?string {
        return $this->id;
    }

    public function getTitle() : string {
        return $this->title;
    }
    public function setTitle(?string $title) : static {
        $this->title = $title ?? '';
        return $this;
    }

    public function getPrice() : float {
        return $this->price;
    }
    public function setPrice(?float $price) : static {
        $this->price = $price ?? 0.0;
        return $this;
    }

    public function getSource() : string {
        return $this->source;
    }
    public function setSource(?string $source) : static {
        $this->source = $source ?? 'local';
        return $this;
    }

    public function getSupplierSku() : string {
        return $this->supplierSku;
    }
    public function setSupplierSku(?string $supplierSku) : static {
        $this->supplierSku = $supplierSku ?? '';
        return $this;
    }
}