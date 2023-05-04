<?php

/*
 * This file is part of the Kimai time-tracking app.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Tests\Invoice\Renderer;

use App\Invoice\Renderer\XmlRenderer;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

/**
 * @covers \App\Invoice\Renderer\AbstractTwigRenderer
 * @covers \App\Invoice\Renderer\XmlRenderer
 * @group integration
 */
class XmlRendererTest extends KernelTestCase
{
    use RendererTestTrait;

    public function testSupports()
    {
        $loader = new FilesystemLoader();
        $env = new Environment($loader);
        $sut = new XmlRenderer($env);

        $this->assertFalse($sut->supports($this->getInvoiceDocument('default.html.twig')));
        $this->assertFalse($sut->supports($this->getInvoiceDocument('freelancer.pdf.twig')));
        $this->assertFalse($sut->supports($this->getInvoiceDocument('timesheet.html.twig')));
        $this->assertFalse($sut->supports($this->getInvoiceDocument('javascript.json.twig')));
        $this->assertFalse($sut->supports($this->getInvoiceDocument('company.docx')));
        $this->assertFalse($sut->supports($this->getInvoiceDocument('spreadsheet.xlsx', true)));
        $this->assertFalse($sut->supports($this->getInvoiceDocument('open-spreadsheet.ods', true)));
        $this->assertFalse($sut->supports($this->getInvoiceDocument('text.txt.twig')));
        $this->assertFalse($sut->supports($this->getInvoiceDocument('javascript.json.twig')));
        $this->assertTrue($sut->supports($this->getInvoiceDocument('xml.xml.twig')));
    }

    public function testRender()
    {
        $kernel = self::bootKernel();
        /** @var Environment $twig */
        $twig = $kernel->getContainer()->get('twig');
        $stack = $kernel->getContainer()->get('request_stack');
        $request = new Request();
        $request->setLocale('en');
        $stack->push($request);

        /** @var FilesystemLoader $loader */
        $loader = $twig->getLoader();
        $loader->addPath($this->getInvoiceTemplatePath(), 'invoice');

        $sut = new XmlRenderer($twig);

        $model = $this->getInvoiceModel();

        $document = $this->getInvoiceDocument('xml.xml.twig');
        $response = $sut->render($document, $model);

        self::assertEquals('application/xml', $response->headers->get('Content-Type'));

        $content = $response->getContent();
        $xml = new \SimpleXMLElement($content);

        $expected = $model->toArray();

        foreach ($xml as $element) {
            $name = $element->getName();
            if ($name === 'items') {
                continue;
            }
            self::assertEquals((string) $expected[$name], (string) $element);
        }
        self::assertEquals(\count($model->getCalculator()->getEntries()), \count($xml->items->item));
    }
}
