<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;


#[UniqueEntity('reference', message: "Cette référence existe déjà. Veuillez en choisir une autre.")]
#[Vich\Uploadable]
#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'products')]
    private ?User $user = null;

    #[Assert\NotBlank(message: "La reference est obligatoire.")]
    #[Assert\Length(
        max: 255,
        maxMessage: 'La reference doit contenir au maximum {{ limit }} caractères.',
    )]
    #[Assert\Regex(
        pattern: "/^[0-9a-zA-Z\-\_\'\ áàâäãåçéèêëíìîïñóòôöõúùûüýÿæœÁÀÂÄÃÅÇÉÈÊËÍÌÎÏÑÓÒÔÖÕÚÙÛÜÝŸÆŒ]+$/i",
        match: true,
        message: 'La reference doit contenir uniquement des lettres, des chiffres le tiret du milieu et l\'undescore.',
    )]
    #[ORM\Column(length: 255)]
    private ?string $reference = null;

    #[Assert\NotBlank(message: "Le nom est obligatoire.")]
    #[Assert\Length(
        max: 255,
        maxMessage: 'Le nom doit contenir au maximum {{ limit }} caractères.',
    )]
    #[Assert\Regex(
        pattern: "/^[0-9a-zA-Z\-\_\'\ áàâäãåçéèêëíìîïñóòôöõúùûüýÿæœÁÀÂÄÃÅÇÉÈÊËÍÌÎÏÑÓÒÔÖÕÚÙÛÜÝŸÆŒ]+$/i",
        match: true,
        message: 'Le nom doit contenir uniquement des lettres, des chiffres le tiret du milieu de l\'undescore.',
    )]
    #[ORM\Column(length: 255, unique: true)]
    private ?string $name = null;

    #[Gedmo\Slug(fields: ['name'])]
    #[ORM\Column(length: 255, unique: true)]
    private ?string $slug = null;

    #[Assert\NotBlank(message: "La description courte est obligatoire.")]
    #[Assert\Length(
        max: 500,
        maxMessage: 'La description courte ne doit pas dépasser {{ limit }} caractères.',
    )]
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $shortDescription = null;

    #[Assert\NotBlank(message: "La description longue est obligatoire.")]
    #[Assert\Length(
        max: 1000,
        maxMessage: 'La description longue ne doit pas dépasser {{ limit }} caractères.',
    )]
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $longDescription = null;

    #[Assert\NotBlank(message: "La marque est obligatoire.")]
    #[Assert\Length(
        max: 255,
        maxMessage: 'La marque doit contenir au maximum {{ limit }} caractères.',
    )]
    #[Assert\Regex(
        pattern: "/^[0-9a-zA-Z\-\_\'\ áàâäãåçéèêëíìîïñóòôöõúùûüýÿæœÁÀÂÄÃÅÇÉÈÊËÍÌÎÏÑÓÒÔÖÕÚÙÛÜÝŸÆŒ]+$/i",
        match: true,
        message: 'La marque doit contenir uniquement des lettres, des chiffres le tiret du milieu de l\'undescore.',
    )]
    #[ORM\Column(length: 255)]
    private ?string $brand = null;

    #[Assert\NotBlank(message: "Le prix de vente est obligatoire.")]
    #[Assert\Regex(
        pattern: "/[0-9]{1,}[.,]{0,1}[0-9]{0,2}/",
        match: true,
        message: 'Le prix de vente doit contenir uniquement des chiffres et un point.',
    )]
    #[ORM\Column]
    private ?float $sellingPrice = null;

    #[Assert\Regex(
        pattern: "/[0-9]{1,}[.,]{0,1}[0-9]{0,2}/",
        match: true,
        message: 'Le prix de la promo doit contenir uniquement des chiffres et un point.',
    )]
    #[ORM\Column(nullable: true)]
    private ?float $discountPrice = null;

    #[Assert\NotBlank(message: "La quantité est obligatoire.")]
    #[Assert\Regex(
        pattern: "/^\d+$/",
        match: true,
        message: 'La quantité doit contenir uniquement des chiffres et un point.',
    )]
    #[ORM\Column(type: Types::BIGINT)]
    private ?string $quantity = null;

    #[ORM\Column(options: ['default' => false])]
    private ?bool $isNewArrival = null;

    #[ORM\Column(options: ['default' => false])]
    private ?bool $isBetterSeller = null;

    #[ORM\Column(options: ['default' => true])]
    private ?bool $isAvailable = null;

    #[ORM\Column(options: ['default' => false])]
    private ?bool $isSpecialOffer = null;

    #[Gedmo\Timestampable(on: 'create')]
    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $createdAt = null;

    #[Gedmo\Timestampable(on: 'update')]
    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[Assert\NotBlank(message: "La catégorie est obligatoire.")]
    #[Assert\Type(
        type: Category::class,
        message: "Veuillez entrer une catégorie valide.",
    )]
    #[ORM\ManyToMany(targetEntity: Category::class, inversedBy: 'products')]
    private Collection $category;

    #[ORM\OneToMany(mappedBy: 'product', targetEntity: Image::class)]
    private Collection $images;

    #[Assert\File(
        maxSize: '4096k',
        extensions: ['jpeg', 'jpg', 'png', 'webp'],
        extensionsMessage: "Seuls les formats d'images jpeg, jpg, png, webp sont autorisés.",
    )]
    #[Vich\UploadableField(mapping: 'product_image', fileNameProperty: 'Image')]
    private ?File $imageFile = null;

    #[ORM\Column(length: 255, nullable: true, unique: true)]
    private ?string $Image = null;

    public function __construct()
    {
        $this->isNewArrival = false;
        $this->isBetterSeller = false;
        $this->isAvailable = true;
        $this->isSpecialOffer = false;
        $this->category = new ArrayCollection();
        $this->images = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): static
    {
        $this->reference = $reference;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getShortDescription(): ?string
    {
        return $this->shortDescription;
    }

    public function setShortDescription(?string $shortDescription): static
    {
        $this->shortDescription = $shortDescription;

        return $this;
    }

    public function getLongDescription(): ?string
    {
        return $this->longDescription;
    }

    public function setLongDescription(?string $longDescription): static
    {
        $this->longDescription = $longDescription;

        return $this;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(string $brand): static
    {
        $this->brand = $brand;

        return $this;
    }

    public function getSellingPrice(): ?float
    {
        return $this->sellingPrice;
    }

    public function setSellingPrice(float $sellingPrice): static
    {
        $this->sellingPrice = $sellingPrice;

        return $this;
    }

    public function getDiscountPrice(): ?float
    {
        return $this->discountPrice;
    }

    public function setDiscountPrice(?float $discountPrice): static
    {
        $this->discountPrice = $discountPrice;

        return $this;
    }

    public function getQuantity(): ?string
    {
        return $this->quantity;
    }

    public function setQuantity(string $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function isIsNewArrival(): ?bool
    {
        return $this->isNewArrival;
    }

    public function setIsNewArrival(bool $isNewArrival): static
    {
        $this->isNewArrival = $isNewArrival;

        return $this;
    }

    public function isIsBetterSeller(): ?bool
    {
        return $this->isBetterSeller;
    }

    public function setIsBetterSeller(bool $isBetterSeller): static
    {
        $this->isBetterSeller = $isBetterSeller;

        return $this;
    }

    public function isIsAvailable(): ?bool
    {
        return $this->isAvailable;
    }

    public function setIsAvailable(bool $isAvailable): static
    {
        $this->isAvailable = $isAvailable;

        return $this;
    }

    public function isIsSpecialOffer(): ?bool
    {
        return $this->isSpecialOffer;
    }

    public function setIsSpecialOffer(bool $isSpecialOffer): static
    {
        $this->isSpecialOffer = $isSpecialOffer;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return Collection<int, Category>
     */
    public function getCategory(): Collection
    {
        return $this->category;
    }

    public function addCategory(Category $category): static
    {
        if (!$this->category->contains($category)) {
            $this->category->add($category);
        }

        return $this;
    }

    public function removeCategory(Category $category): static
    {
        $this->category->removeElement($category);

        return $this;
    }

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile|null $imageFile
     */
    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    /**
     * @return Collection<int, Image>
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Image $image): static
    {
        if (!$this->images->contains($image)) {
            $this->images[] = $image;
            $image->setProduct($this);
        }

        return $this;
    }

    public function removeImage(Image $image): static
    {
        if ($this->images->contains($image)) {
            $this->images->removeElement($image);
            // set the owning side to null (unless already changed)
            if ($image->getProduct() === $this) {
                $image->setProduct(null);
            }
        }

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->Image;
    }

    public function setImage(?string $Image): static
    {
        $this->Image = $Image;

        return $this;
    }
}
