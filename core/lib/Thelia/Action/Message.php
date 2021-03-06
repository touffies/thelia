<?php
/*************************************************************************************/
/*      This file is part of the Thelia package.                                     */
/*                                                                                   */
/*      Copyright (c) OpenStudio                                                     */
/*      email : dev@thelia.net                                                       */
/*      web : http://www.thelia.net                                                  */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE.txt  */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/

namespace Thelia\Action;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Thelia\Model\MessageQuery;
use Thelia\Model\Message as MessageModel;

use Thelia\Core\Event\TheliaEvents;

use Thelia\Core\Event\Message\MessageUpdateEvent;
use Thelia\Core\Event\Message\MessageCreateEvent;
use Thelia\Core\Event\Message\MessageDeleteEvent;

class Message extends BaseAction implements EventSubscriberInterface
{
    /**
     * Create a new messageuration entry
     *
     * @param \Thelia\Core\Event\Message\MessageCreateEvent $event
     */
    public function create(MessageCreateEvent $event)
    {
        $message = new MessageModel();

        $message
            ->setDispatcher($event->getDispatcher())

            ->setName($event->getMessageName())

            ->setLocale($event->getLocale())
            ->setTitle($event->getTitle())

            ->setSecured($event->getSecured())

            ->save()
        ;

        $event->setMessage($message);
    }

    /**
     * Change a message
     *
     * @param \Thelia\Core\Event\Message\MessageUpdateEvent $event
     */
    public function modify(MessageUpdateEvent $event)
    {

        if (null !== $message = MessageQuery::create()->findPk($event->getMessageId())) {

            $message
                ->setDispatcher($event->getDispatcher())

                ->setName($event->getMessageName())
                ->setSecured($event->getSecured())

                ->setLocale($event->getLocale())

                ->setTitle($event->getTitle())
                ->setSubject($event->getSubject())

                ->setHtmlMessage($event->getHtmlMessage())
                ->setTextMessage($event->getTextMessage())

                ->setHtmlLayoutFileName($event->getHtmlLayoutFileName())
                ->setHtmlTemplateFileName($event->getHtmlTemplateFileName())
                ->setTextLayoutFileName($event->getTextLayoutFileName())
                ->setTextTemplateFileName($event->getTextTemplateFileName())

                ->save();

            $event->setMessage($message);
        }
    }

    /**
     * Delete a messageuration entry
     *
     * @param \Thelia\Core\Event\Message\MessageDeleteEvent $event
     */
    public function delete(MessageDeleteEvent $event)
    {

        if (null !== ($message = MessageQuery::create()->findPk($event->getMessageId()))) {

            $message
                ->setDispatcher($event->getDispatcher())
                ->delete()
            ;

            $event->setMessage($message);
        }
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            TheliaEvents::MESSAGE_CREATE   => array("create", 128),
            TheliaEvents::MESSAGE_UPDATE   => array("modify", 128),
            TheliaEvents::MESSAGE_DELETE   => array("delete", 128),
        );
    }
}
