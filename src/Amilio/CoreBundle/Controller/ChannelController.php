<?php
namespace Amilio\CoreBundle\Controller;

use Symfony\Component\HttpFoundation\File\UploadedFile;

use Amilio\CoreBundle\Entity\ChannelRepository;

use Amilio\CoreBundle\Form\Type\ChannelType;
use Amilio\CoreBundle\Entity\Channel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ChannelController extends Controller
{

    public function newAction()
    {
        $form = $this->createForm(new ChannelType(), new Channel(), array(
            'action' => $this->generateUrl('amilio_core_channel_store')
        ));
        
        return $this->render('AmilioCoreBundle:Channel:new.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * This function moves the uploaded file to the given upload dir and renames it.
     * 
     * @param UploadedFile $file
     * @return string the new filename
     */
    private function handleFileUpload(UploadedFile $file) {
        // @todo check for image extension
        // @todo use a config file for the path
        $extension = $file->guessExtension();
        
        $filename = md5($this->getUser()->getUsername() . time() ) . '.' . $extension;
        
        $newFile = __DIR__ . "/../../../../web/upload/".$filename;
        
        rename($file->getPath() . DIRECTORY_SEPARATOR . $file->getFilename(), $newFile);
        
        return '/upload/'.$filename;        
    }
    
    public function storeAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(new ChannelType(), new Channel(), array(
            'action' => $this->generateUrl('amilio_core_channel_store')
        ));
        
        $form->handleRequest($request);
        
        if ($form->isValid()) {
            $channel = $form->getData();
            
            $user = $this->getUser();
            
            $channel->setType(Channel::TYPE_STANDARD);
            $channel->setOwner($user); 
                       
            if(array_key_exists('image', $form)) {                
                $channel->setImage($this->handleFileUpload($form['image']->getData()));
            }

            $user->addChannel($channel);
            
            $em->persist($channel);
            $em->persist($user);
            
            $em->flush();
            
            return $this->redirect($this->generateUrl('amilio_core_channel_list'));
        }
        
        return $this->render('AmilioCoreBundle:Channel:new.html.twig', array(
            'form' => $form->createView()
        ));
    }

    public function listAction()
    {
        $user = $this->getUser();
        $channels = $user->getChannels();
        
        return $this->render('AmilioCoreBundle:Channel:list.html.twig', array(
            'channels' => $channels
        ));
    }

    public function showAction(Channel $channel, $canonicalName)
    {
        if ($channel->getCanonicalName() != $canonicalName) {
            return $this->redirect($this->generateUrl('amilio_core_channel_show', array('channel' => $channel->getId(), 'canonicalName' => $channel->getCanonicalName())), 302);
        }
        
        return $this->render('AmilioCoreBundle:Channel:show.html.twig', array(
            'channel' => $channel,
            'products' => $channel->getProducts(),
        ));
    }
}