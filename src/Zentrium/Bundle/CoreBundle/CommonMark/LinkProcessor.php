<?php

namespace Zentrium\Bundle\CoreBundle\CommonMark;

use League\CommonMark\Block\Element\Document;
use League\CommonMark\DocumentProcessorInterface;
use League\CommonMark\Inline\Element\Link;

class LinkProcessor implements DocumentProcessorInterface
{
    public function processDocument(Document $document)
    {
        $walker = $document->walker();
        while ($event = $walker->next()) {
            $node = $event->getNode();
            if (!($node instanceof Link) || !$event->isEntering()) {
                continue;
            }

            $attributes = $node->getData('attributes', []);
            $attributes['rel'] = 'noreferrer';
            $node->data['attributes'] = $attributes;
        }
    }
}
