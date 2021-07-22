<?php

namespace App\Form\Hr;

use App\Entity\Hr\AlumnusContact;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class AlumnusContactHandler
{
    protected $form;

    protected $request;

    protected $em;

    public function __construct(FormInterface $form, Request $request, EntityManager $em)
    {
        $this->form = $form;
        $this->request = $request;
        $this->em = $em;
    }

    public function process()
    {
        if ('POST' == $this->request->getMethod()) {
            $this->form->handleRequest($this->request);

            if ($this->form->isValid()) {
                $this->onSuccess($this->form->getData());

                return true;
            }
        }

        return false;
    }

    public function onSuccess(AlumnusContact $alumnuscontact)
    {
        $this->em->persist($alumnuscontact);

        $this->em->flush();
    }
}
