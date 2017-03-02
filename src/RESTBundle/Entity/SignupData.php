<?php

namespace RESTBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;

class SignupData
{
	/* Person Fields */
    /**
     * @Assert\Type(type="int")
     */
    public $expaId;

    /**
     * @Assert\NotBlank()
     */
    public $firstname;

    /**
     * @Assert\NotBlank()
     */
    public $lastName;

    /**
     * @Assert\Email
     * @Assert\NotBlank()
     */
    public $email;

    public $phone;

    public $city;

    public $country = 'Germany'

    public $gender;

    public $birthday;

    /**
     * @Assert\Type(type="int")
     * @Assert\NotBlank()
     */
    public $homeLc;

    /**
     * @Assert\Type(type="int")
     */
    public $homeMc = 1596;

    /**
     * @Assert\Type(type="array")
     */
    public $howHeard;

    public $leadSource;


    /* EP Fields*/
    public $academicExperience;

    public $ProfessionalExperience;

    public $languages;

    public $comment;

    public $skills;


    /* Member Fields*/
    /**
     * @Assert\DateTime()
     */
    public $intendedSemesterAbroadStartDate;

    /**
     * @Assert\DateTime()
     */
    public $intendedSemesterAbroadEndDate;

    public $besidesAiesec;

    /*
     * neither professional nor academic
     * e.g. has been abroad or voluntary work
     */
    public $experience

    public $applicationMotivation;

    public $linkedin;

    public $skillsToBeLearned;

    public $functionalAreaInterestedIn;

    public $jdInterestedIn;

    public $hoursToBeInvested;


    /* Exchange Fields */
    public $focusOfInternship;

    /**
     * @Assert\DateTime()
     */
    public $earliestStartDate;

    /**
     * @Assert\DateTime()
     */
    public $latestEndDate;

    /**
     * @Assert\Type(type="int")
     */
    public $durationMin;

    /**
     * @Assert\Type(type="int")
     */
    public $durationMax;
    
    /**
     * @Assert\Type(type="array")
     */
    public $worldRegions;

    public function sendNotificationMail()
    {
        
    }
}