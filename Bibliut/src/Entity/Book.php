<?php

namespace App\Entity;

use App\Repository\BookRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BookRepository::class)]
class Book
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[Groups("basic")]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups("basic")]
    #[ORM\Column(length: 5000)]
    private ?string $title = null;

    #[Groups("basic_image")]
    #[ORM\Column(type: Types::BLOB, nullable: true)]
    private $image = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $release_date = null;

    #[ORM\Column(nullable: true)]
    private ?int $nb_pages = null;


    #[ORM\Column(length: 10000, nullable: true)]
    private ?string $summary = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $acquisition_date = null;

    #[ORM\OneToMany(mappedBy: 'book', targetEntity: Borrow::class)]
    private Collection $loans;

    #[Groups("basic")]
    #[ORM\ManyToMany(targetEntity: Author::class, inversedBy: 'books')]
    private Collection $authors;

    #[Groups("basic")]
    #[ORM\ManyToMany(targetEntity: Category::class, inversedBy: 'books')]
    private Collection $categories;

    #[Groups("basic")]
    #[ORM\ManyToOne(inversedBy: 'books')]
    private ?Language $language = null;

    #[Groups("basic")]
    #[ORM\ManyToOne(inversedBy: 'books')]
    private ?Editor $editor = null;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->authors = new ArrayCollection();
        $this->loans = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }


    public function getImage()
    {
        if ($this->image != null) {
            return stream_get_contents($this->image);
        }
    }

    public function setImage($image): self
    {
        $this->image = $image;

        return $this;
    }

    #[Groups("basic")]
    public function getReleaseDate(): ?\DateTimeInterface
    {
        return $this->release_date;
    }

    public function setReleaseDate(\DateTimeInterface $release_date): self
    {
        $this->release_date = $release_date;

        return $this;
    }

    #[Groups("basic")]
    public function getNbPages(): ?int
    {
        return $this->nb_pages;
    }

    public function setNbPages(?int $nb_pages): self
    {
        $this->nb_pages = $nb_pages;

        return $this;
    }

    #[Groups("basic")]
    public function getSummary(): ?string
    {
        return $this->summary;
    }

    public function setSummary(?string $summary): self
    {
        $this->summary = $summary;

        return $this;
    }

    public function getAcquisitionDate(): ?\DateTimeInterface
    {
        return $this->acquisition_date;
    }

    public function setAcquisitionDate(\DateTimeInterface $acquisition_date): self
    {
        $this->acquisition_date = $acquisition_date;

        return $this;
    }

    /**
     * @return Collection<int, Borrow>
     */
    public function getLoans(): Collection
    {
        return $this->loans;
    }

    public function addLoan(Borrow $loan): self
    {
        if (!$this->loans->contains($loan)) {
            $this->loans->add($loan);
            $loan->setBook($this);
        }

        return $this;
    }

    public function removeLoan(Borrow $loan): self
    {
        if ($this->loans->removeElement($loan)) {
            // set the owning side to null (unless already changed)
            if ($loan->getBook() === $this) {
                $loan->setBook(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Author>
     */
    public function getAuthors(): Collection
    {
        return $this->authors;
    }

    public function addAuthor(Author $author): self
    {
        if (!$this->authors->contains($author)) {
            $this->authors->add($author);
        }

        return $this;
    }

    public function removeAuthor(Author $author): self
    {
        $this->authors->removeElement($author);

        return $this;
    }

    /**
     * @return Collection<int, Category>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
        }

        return $this;
    }

    public function removeCategory(Category $category): self
    {
        $this->categories->removeElement($category);

        return $this;
    }

    public function getLanguage(): ?Language
    {
        return $this->language;
    }

    public function setLanguage(?Language $language): self
    {
        $this->language = $language;

        return $this;
    }

    public function getEditor(): ?Editor
    {
        return $this->editor;
    }

    public function setEditor(?Editor $editor): self
    {
        $this->editor = $editor;

        return $this;
    }
}
