<?php
namespace Amilio\CoreBundle\Controller;

use Amilio\CoreBundle\Entity\ChannelElement;

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

use Amilio\CoreBundle\Entity\Product;

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
        $channel = new Channel();
        $form = $this->createForm(new ChannelType(), $channel, array(
            'action' => $this->generateUrl('amilio_core_channel_store')
        ));
        
        return $this->render('AmilioCoreBundle:Channel:new.html.twig', array(
            'form' => $form->createView(), 'channel' => $channel
        ));
    }
    
    public function editAction(Channel $channel)
    {
        $channel->setImage('');
        $form = $this->createForm(new ChannelType(), $channel, array(
            'action' => $this->generateUrl('amilio_core_channel_store', array('channelId' => $channel->getId()))
        ));
    
        return $this->render('AmilioCoreBundle:Channel:new.html.twig', array(
            'form' => $form->createView(), 'channel' => $channel
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
    
    public function storeAction(Request $request, $channelId)
    {
        if ($channelId == -1) {
            $channel = new Channel();
            $imageName = "";
        }else{
            $channel = $this->getDoctrine()->getRepository('AmilioCoreBundle:Channel')->find($channelId);
            $imageName = $channel->getImage();
            $channel->setImage("");
        }
               
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(new ChannelType(), $channel, array(
            'action' => $this->generateUrl('amilio_core_channel_store', array('channelId' => $channelId))
        ));
        
        $form->handleRequest($request);
        
        if ($form->isValid()) {
            $channel = $form->getData();
            
            $user = $this->getUser();
            
            $channel->setType(Channel::TYPE_CHANNEL);
            $channel->setOwner($user); 
                       
            var_dump( $form['image']->getData());
            
            if($form->has("image") && (!is_null($form->get("image")->getData()))) {
                $channel->setImage($this->handleFileUpload($form['image']->getData()));
            }else{
                $channel->setImage($imageName);
            }
            
            if ($channelId == -1) {
                $user->addChannel($channel);
                $em->persist($user);
            }
            
            $em->persist($channel);
            
            $em->flush();
            
            return $this->redirect($this->generateUrl('amilio_core_channel_show', array('channel' => $channel->getId(), 'canonicalName' => $channel->getCanonicalName())));
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
            'elements' => $channel->getElements(),
        ));
    }

    public function showHottestAction()
    {
        $newest = $this->getDoctrine()->getRepository('AmilioCoreBundle:Channel')->findBy(array(), array('id' => 'DESC'), 20, 0);
        $channelsOfTheWeek = $this->getDoctrine()->getRepository('AmilioCoreBundle:Channel')->findChannelsOfTheWeek();
        
        return $this->render('AmilioCoreBundle:Channel:hottest.html.twig', array(
            'newest' => $newest, 'channelsOfTheWeek' => $channelsOfTheWeek
        ));
    }

    public function storeHeaderImageAction(Request $request, Channel $channel) 
    {
        $image = $request->get('header_image');
        var_dump( $image );
        die;        
        return $this->redirect($this->generateUrl('amilio_core_channel_show', array('channel' => $channel->getId(), 'canonicalName' => $channel->getCanonicalName())));
    }
    
    public function deleteAction(Request $request, Channel $channel) 
    {
        if( $request->getMethod() == "POST" ) {
//             $channel = $this->getDoctrine()->getRepository('AmilioCoreBundle:Channel')->find($request->get("channelId"));
            
            $owner = $channel->getOwner();
            
            if( $owner->getId() == $this->getUser()->getId()) {
                $em = $this->getDoctrine()->getManager();
                $em->remove($channel);
                $em->flush();
                return $this->redirect($this->generateUrl("amilio_core_channel_list"));
            }
        }
        throw new AccessDeniedHttpException();
    }

    public function showElementAction(ChannelElement $element)
    {
        $item = $this->getDoctrine()->getRepository($element->getType())->find($element->getForeignId());
        
        $template = str_replace("\\", "_", get_class($item)) . ".html.twig";
        
        return $this->render('AmilioCoreBundle:Channel:' . $template, array("item" => $item, 'element' => $element, 'channel' => $element->getChannel() ));
    }
}
