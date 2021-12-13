<?php

/*
 * This file was copied from the FOSRestBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file at https://github.com/FriendsOfSymfony/FOSRestBundle/blob/master/LICENSE
 *
 * Original @author Ener-Getick <egetick@gmail.com>
 */

namespace App\Lib;

use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;

/**
 * Serializes invalid Form instances.
 */
class FormErrorSerializer
{
    /**
     * @param FormInterface $data
     *
     * @return array
     */
    public function convertFormToArray(FormInterface $data): array
    {
        $form = $errors = [];

        /** @var FormError $error */
        foreach ($data->getErrors() as $error) {
            $errors[] = $error->getMessage();
        }

        if ($errors) {
            $form['errors'] = $errors;
        }

        $children = [];
        foreach ($data->all() as $child) {
            if ($child instanceof FormInterface) {
                $children[$child->getName()] = $this->convertFormToArray($child);
            }
        }

        if ($children) {
            $form['children'] = $children;
        }

        return $form;
    }
}
