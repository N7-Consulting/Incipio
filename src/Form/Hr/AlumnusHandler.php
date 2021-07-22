<?php

namespace App\Form\Hr;

use App\Entity\Hr\Alumnus;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class AlumnusHandler
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

    public function onSuccess(Alumnus $alumnus)
    {
        $this->em->persist($alumnus);

        $this->em->flush();
    }
}
