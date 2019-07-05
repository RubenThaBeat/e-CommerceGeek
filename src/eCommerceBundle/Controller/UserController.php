<?php

namespace eCommerceBundle\Controller;

use eCommerceBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use eCommerceBundle\Form\UserType;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * User controller.
 *
 * @Route("/user")
 */
class UserController extends Controller
{
    /**
     * 
     *
     * @Route("/all", name="user_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $users = $em->getRepository(User::class)->findAll();

        return $this->render('@eCommerce/User/index.html.twig', ['users' => $users]);
    }

    /**
     * @Route("/show/{id}", name="user_show")
     * @Method("GET")
     */
    public function showAction(User $user)
    {

        return $this->render('@eCommerce/User/show.html.twig', ['user' => $user]);
    }

    /**
     * @Route("/edit/{id}", name="user_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, UserPasswordEncoderInterface $passwordEncoder, User $user)
    {
        $editForm = $this->createForm(UserType::class, $user);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('user_index');
        }

        return $this->render('@eCommerce/User/edit.html.twig', array(
            'user' => $user,
            'edit_form' => $editForm->createView()
        ));
    }

    /**
     * @Route("/{id}", name="user_delete")
     * 
     */
    public function deleteAction( User $user)
    {
            $em = $this->getDoctrine()->getManager();
            $em->remove($user);
            $em->flush();

        return $this->redirectToRoute('user_index');
    }
}
