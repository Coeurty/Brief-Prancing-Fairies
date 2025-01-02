<?php

// src/Controller/UserController.php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class UserController extends AbstractController
{
    private $entityManager;

    // Inject the EntityManagerInterface into the controller
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/account', name: 'app_account')]
    public function account(Request $request, UserInterface $user)
    {
        // Create the form with the current user data
        $form = $this->createForm(UserType::class, $user);

        // Handle the form submission
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $profilePicture */
            $profilePicture = $form->get('profilePicture')->getData();

            if ($profilePicture) {
                // Generate a unique file name to avoid overwriting existing files
                $newFilename = uniqid() . '.' . $profilePicture->guessExtension();

                try {
                    // Move the file to the directory where profile pictures are stored
                    $profilePicture->move(
                        $this->getParameter('user_avatar_directory'), // Directory to store the uploaded image
                        $newFilename
                    );
                    // Set the new filename in the user entity
                    $user->setProfilePicture($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Error uploading the image.');
                }
            }

            // Save the user with the new profile picture
            $this->entityManager->flush();

            // Success message
            $this->addFlash('success', 'Your profile has been updated.');

            // Redirect to avoid re-submitting the form
            return $this->redirectToRoute('app_account');
        }

        // Render the template with the form variable passed to it
        return $this->render('account/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}

