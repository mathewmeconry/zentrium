<?php

namespace Vkaf\Bundle\OafBundle\Tests\Kiosk\Slide;

use PHPUnit\Framework\TestCase;
use Vkaf\Bundle\OafBundle\Kiosk\Slide\SlideManager;

class SlideManagerTest extends TestCase
{
    public function testRender()
    {
        $slideOptions = ['a' => 'b'];
        $slideResult = ['c' => 'd'];
        $templatingResult = '<html></html>';

        $templating = $this->createMock('Symfony\Component\Templating\EngineInterface');
        $templating->expects($this->once())
            ->method('render')
            ->with($this->identicalTo('VkafOafBundle:Kiosk:foo.html.twig'), $this->identicalTo($slideResult))
            ->will($this->returnValue($templatingResult));

        $slide = $this->createMock('Vkaf\Bundle\OafBundle\Kiosk\Slide\SlideInterface');
        $slide->expects($this->once())
            ->method('render')
            ->with($this->identicalTo($slideOptions))
            ->will($this->returnValue($slideResult));

        $manager = new SlideManager($templating);
        $manager->registerType('foo', $slide);

        $response = $manager->render('foo', $slideOptions, []);

        $this->assertSame($templatingResult, $response->getContent());
    }

    /**
     * @expectedException \Vkaf\Bundle\OafBundle\Kiosk\Slide\RenderException
     */
    public function testRenderInvalidType()
    {
        $templating = $this->createMock('Symfony\Component\Templating\EngineInterface');
        $manager = new SlideManager($templating);

        $manager->render('invalidtype', [], []);
    }
}
