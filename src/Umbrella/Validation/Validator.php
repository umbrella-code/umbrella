<?php

namespace Umbrella\Validation;

class Validator
{
    /**
     * If the validator failed or not
     *
     * @var boolean
     */
    private $failed = false;

    /**
     * If the validator passed or not
     *
     * @var boolean
     */
    private $passed = true;

    /**
     * Instance of the Symfony LegacyValidator
     */
    private $sValidator;

    /**
     * Array of violation messages
     *
     * @var array
     */
    private $errors = array();

    /**
     * Umbrella Validator Constructor
     *
     * @param  \Symfony\Component\Validator\LegacyValidator $sValidator
     * @return void
     */
    public function __construct($sValidator)
    {
        $this->sValidator = $sValidator;
    }

    /**
     * Validate the entity
     *
     * @return void
     */
    public function validate($value, $groups = null, $traverse = false, $deep = false)
    {
        $violations = $this->sValidator->validate($value, $groups, $traverse, $deep);

        if(count($violations) >= 1)
        {
            $this->hasFailed();

            foreach($violations as $key => $val)
            {
                $this->errors[] = $violations[$key]->getMessage();
            }
        }
        else
        {
            $this->hasPassed();
        }
    }

    /**
     * Set booleans if validator failed
     *
     * @return void
     */
    public function hasFailed()
    {
        if($this->failed == false || $this->passed == true)
        {
            $this->failed = true;
            $this->passed = false;
        }
    }

    /**
     * Set booleans if validator passed
     *
     * @return void
     */
    public function hasPassed()
    {
        if($this->passed == false || $this->failed == true)
        {
            $this->passed = true;
            $this->failed = false;
        }
    }

    /**
     * Get $this->failed
     *
     * @return \Umbrella\Validation\Validator:$failed
     */
    public function failed()
    {
        return $this->failed;
    }

    /**
     * Get $this->passed
     *
     * @return \Umbrella\Validation\Validator:$passed
     */
    public function passed()
    {
        return $this->passed;
    }

    /**
     * Gets all errors
     *
     * @return \Umbrella\Validation\Validator:$errors
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Gets an error at a certain index
     *
     * @param  integer $index
     * @return \Umbrella\Validation\Validator:$error
     */
    public function getError($index)
    {
        return $error = array($this->errors[$index]);
    }
}
