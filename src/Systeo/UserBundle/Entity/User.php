<?php

namespace Systeo\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="Systeo\UserBundle\Repository\UserRepository")
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity(fields="username",message="Veuillez choisir un autre identifiant!")
 */
class User implements UserInterface
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="first_name", type="string", length=255)
     * @Assert\NotBlank(message="le prénom est obligatoire")
     */
    private $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="last_name", type="string", length=255)
     * @Assert\NotBlank(message="le nom est obligatoire")
     */
    private $lastName;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, unique=true)
     * @Assert\NotBlank(message="l'email est obligatoire")
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=255, unique=true)
     * @Assert\NotBlank(message="l'identifiant est obligatoire")
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255)
     * @Assert\NotBlank(message="le mot de passe est obligatoire")
     */
    private $password;

    private $password_edit;
    
    private $search;
    
    private $prenomNom;
    
    /**
     * @var string
     *
     * @ORM\Column(name="apikey", type="string", length=255, unique=true)
     */
    private $apikey;

    /**
     * @var array
     *
     * @ORM\Column(name="roles", type="array")
     */
    private $roles;
    
    /**
     * @var binary
     *
     * @ORM\Column(name="active", type="boolean")
     */
    private $active;
    
    /**
    * @ORM\ManyToMany(targetEntity="Systeo\UserBundle\Entity\Team", inversedBy="users", cascade={"persist"})
    * @ORM\JoinTable(name="team_user")
    */
    private $teams;

    public function __construct() {
        $this->roles = [
            'Admin'=>'ROLE_ADMIN',
            'Utilisateur'=>'ROLE_USER',
            ];
        
        $this->teams = new \Doctrine\Common\Collections\ArrayCollection();
    }
    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set firstName
     *
     * @param string $firstName
     *
     * @return User
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     *
     * @return User
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set username
     *
     * @param string $username
     *
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }
    
    public function getPasswordEdit()
    {
        return $this->password_edit;
    }
    
    public function setPasswordEdit($passwordEdit)
    {
        $this->password_edit = $passwordEdit;
        
        return $this;
    }


    /**
     * Get salt
     *
     * @return string
     */
    public function getSalt()
    {
       
    }

    /**
     * Set apikey
     *
     * @param string $apikey
     *
     * @return User
     */
    public function setApikey($apikey)
    {
        $this->apikey = $apikey;

        return $this;
    }

    /**
     * Get apikey
     *
     * @return string
     */
    public function getApikey()
    {
        return $this->apikey;
    }

    /**
     * Set roles
     *
     * @param array $roles
     *
     * @return User
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Get roles
     *
     * @return array
     */
    public function getRoles()
    {
        return $this->roles;
    }

    public function eraseCredentials() {
        
    }
    
    /**
     * @ORM\PrePersist
     */
    public function generateApikey(){
        
        $apikey = '';
        
        $str = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        
        for($i = 0;  $i<40; $i++){
           $apikey .= $str[rand(0,61)]; 
        }
        
        $this->setApikey($apikey);
        
    }
    
    /** @see \Serializable::serialize() */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->password,
            // see section on salt below
            // $this->salt,
        ));
    }

    /** @see \Serializable::unserialize() */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->password,
            // see section on salt below
            // $this->salt
        ) = unserialize($serialized);
    }
    

    /**
     * Set active
     *
     * @param boolean $active
     *
     * @return User
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean
     */
    public function getActive()
    {
        return $this->active;
    }
    
    
    /**
     * Set search
     *
     * @param string $search
     *
     * @return User
     */
    public function setSearch($search)
    {
        $this->search = $search;

        return $this;
    }

    /**
     * Get search
     *
     * @return string
     */
    public function getSearch()
    {
        return $this->search;
    }
    
    public function getPrenomNom()
    {
        return $this->getFirstName().' '.$this->getLastName();
    }
    

    /**
     * Add team
     *
     * @param \Systeo\UserBundle\Entity\Team $team
     *
     * @return User
     */
    public function addTeam(\Systeo\UserBundle\Entity\Team $team)
    {
        $this->teams[] = $team;

        return $this;
    }

    /**
     * Remove team
     *
     * @param \Systeo\UserBundle\Entity\Team $team
     */
    public function removeTeam(\Systeo\UserBundle\Entity\Team $team)
    {
        $this->teams->removeElement($team);
    }

    /**
     * Get teams
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTeams()
    {
        return $this->teams;
    }
    
    /**
     * Check if user is Admin
     * @return BOOL
     */
    public function isAdmin(){
        
        return in_array('ROLE_ADMIN', $this->getRoles());
        
    }
    
    /**
     * Check if user is Admin
     * @return BOOL
     */
    public function isManager(){
        
        return in_array('ROLE_MANAGER', $this->getRoles());
        
    }
}
