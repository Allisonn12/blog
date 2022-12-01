<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category
{
    /*
        =====================================================================
            Relationship
        =====================================================================
        One category can have many posts, but one post can have only one category
    */ 

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * ORM/Column(type="string", length=255)
     * */ 
    private mixed $title;

    /**
     * ORM/OneToMany(targetEntity=Posts::class, mappedBy="category")
     * */
    private $posts;

    public function getId(): ?int
    {
        return $this->id;
    }


    /**
     * @return
     * */ 
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param $title
     * @return void
     */
    public function setTitle($title):void{
        $this->title = $title;
    }



    /**
     * @return mixed
     */
    public function getPosts()
    {
        return $this->posts;
    }

    public function addPost(Post $post){
        if(!$this->posts->contains($post)){
            $this->posts[] = $post;
            $post->setCategory($this);
        }
        return $this;
    }

    public function removePost(Post $post){
        if($this->posts->contains($post)){
           $this->posts->removeElement($post);
           if ($post->getCategory() === $this){
               $post->setCategory(null);
           }
        }
        return $this;
    }

}
