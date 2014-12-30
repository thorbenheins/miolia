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
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Cookie;

class ChannelController extends Controller
{

    public function newAction($parentId)
    {
        $channel = new Channel();

        $form = $this->createForm(new ChannelType(), $channel, array('action' => $this->generateUrl('amilio_core_channel_store')));

        $form->get('parent_id')->setData($parentId);

        return $this->render('AmilioCoreBundle:Channel:new.html.twig', array('form' => $form->createView(), 'channel' => $channel));
    }

    public function editAction(Channel $channel)
    {
        $channel->setImage('');
        $form = $this->createForm(new ChannelType(), $channel, array('action' => $this->generateUrl('amilio_core_channel_store', array('channelId' => $channel->getId()))));

        if ($channel->getParent()) {
            $form->get('parent_id')->setData($channel->getParent()->getId());
        }
        return $this->render('AmilioCoreBundle:Channel:new.html.twig', array('form' => $form->createView(), 'channel' => $channel));
    }

    /**
     * This function moves the uploaded file to the given upload dir and renames it.
     *
     * @param UploadedFile $file
     * @return string the new filename
     */
    private function handleFileUpload(UploadedFile $file)
    {
        // @todo use a config file for the path
        $extension = $file->guessExtension();

        if (in_array($file->getMimeType(), array("image/png", "image/jpg"))) {
            $filename = md5($this->getUser()->getUsername() . time()) . '.' . $extension;
            $newFile = __DIR__ . "/../../../../web/upload/" . $filename;
            rename($file->getPath() . DIRECTORY_SEPARATOR . $file->getFilename(), $newFile);
            return '/upload/' . $filename;
        }

        return '';
    }

    public function storeAction(Request $request, $channelId)
    {
        if ($channelId == -1) {
            $channel = new Channel();
            $imageName = "";
            $update = false;
        } else {
            $channel = $this->getDoctrine()->getRepository('AmilioCoreBundle:Channel')->find($channelId);
            $imageName = $channel->getImage();
            $channel->setImage("");
            $update = true;
        }

        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(new ChannelType(), $channel, array('action' => $this->generateUrl('amilio_core_channel_store', array('channelId' => $channelId))));

        $form->handleRequest($request);

        if ($form->isValid()) {
            $channel = $form->getData();

            $user = $this->getUser();

            $channel->setType(Channel::TYPE_CHANNEL);
            $channel->setOwner($user);

            $parentId = $form->get('parent_id')->getData();
            if ($parentId > 0 && !$update) {
                $parent = $this->getDoctrine()->getRepository('AmilioCoreBundle:Channel')->find($parentId);
                $channel->setParent($parent);

                $element = new ChannelElement();

                $em->persist($channel);
                $em->flush();

                $element->setElement($channel);
                $element->setChannel($parent);
                $em->persist($element);

                $channel->addElement($element);
                $em->persist($channel);
            }

            if ($form->has("image") && (!is_null($form->get("image")->getData()))) {
                $channel->setImage($this->handleFileUpload($form['image']->getData()));
            } else {
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

        return $this->render('AmilioCoreBundle:Channel:new.html.twig', array('form' => $form->createView()));
    }

    public function listAction()
    {
        $user = $this->getUser();
        $channels = $user->getChannels();

        $favChannel = $this->getDoctrine()->getRepository('AmilioCoreBundle:Channel')->find(13);

        return $this->render('AmilioCoreBundle:Channel:list.html.twig', array('myChannels' => $channels, 'favChannel' => $favChannel));
    }

    public function showAction(Channel $channel, $canonicalName)
    {
        if ($channel->getCanonicalName() != $canonicalName) {
            return $this->redirect($this->generateUrl('amilio_core_channel_show', array('channel' => $channel->getId(), 'canonicalName' => $channel->getCanonicalName())), 302);
        }

        $template = 'AmilioCoreBundle:Channel:themes/' . $channel->getTheme() . '.html.twig';

        return $this->render($template, array('channel' => $channel,));
    }

    public function showHottestAction()
    {
        $newest = $this->getDoctrine()->getRepository('AmilioCoreBundle:Channel')->findNewest();
        $mostFollowed = $this->getDoctrine()->getRepository('AmilioCoreBundle:Channel')->findMostFollowed();
        $favChannel = $this->getDoctrine()->getRepository('AmilioCoreBundle:Channel')->find(13);

        return $this->render('AmilioCoreBundle:Channel:hottest.html.twig', array('favChannel' => $favChannel, 'newestChannels' => $newest, 'mostFollowedChannels' => $mostFollowed));
    }

    public function deleteAction(Request $request, Channel $channel)
    {
        if ($request->getMethod() == "POST") {

            $owner = $channel->getOwner();

            if ($owner->getId() == $this->getUser()->getId()) {
                $em = $this->getDoctrine()->getManager();
                $em->remove($channel);

                $channelElements = $this->getDoctrine()->getRepository('AmilioCoreBundle:ChannelElement')->findBy(array('foreignId' => $channel->getId(), 'type' => 'Amilio\CoreBundle\Entity\Channel'));
                foreach ($channelElements as $element) {
                    $em->remove($element);
                }

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
        $template = str_replace("Proxies___CG___", "", $template);
        return $this->render('AmilioCoreBundle:Channel:' . $template, array("item" => $item, 'element' => $element, 'channel' => $element->getChannel()));
    }

    public function addFavouriteAction(Channel $channel)
    {
        $user = $this->getUser();
        $user->addFavouriteChannel($channel);

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);

        $channel->increaseFollowerCount();
        $em->persist($channel);

        $em->flush();

        $this->setFavCookie();

        return $this->redirect($this->generateUrl('amilio_core_channel_show', array('channel' => $channel->getId(), 'canonicalName' => $channel->getCanonicalName())));
    }

    public function removeFavouriteAction(Channel $channel)
    {
        $user = $this->getUser();

        $user->removeFavouriteChannel($channel);

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);

        $channel->decreaseFollowerCount();
        $em->persist($channel);

        $em->flush();

        $this->setFavCookie();

        return $this->redirect($this->generateUrl('amilio_core_channel_show', array('channel' => $channel->getId(), 'canonicalName' => $channel->getCanonicalName())));
    }

    private function setFavCookie()
    {
        $response = new Response();

        $channels = $this->getUser()->getFavouriteChannels();
        $channelString = "-0-";
        foreach ($channels as $channel) {
            $channelString .= $channel->getId() . "-";
        }

        $response->headers->setCookie(new Cookie("favs", $channelString, 0, '/', null, false, false));

        $response->send();
    }
}