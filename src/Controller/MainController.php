<?php  namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use SwissPaymentSlip\SwissPaymentSlip\OrangePaymentSlipData;
use SwissPaymentSlip\SwissPaymentSlip\OrangePaymentSlip;
use SwissPaymentSlip\SwissPaymentSlipTcpdf\PaymentSlipTcpdf;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;


class MainController extends Controller 
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {

        // Create an instance of TCPDF, setup default settings
        $tcPdf = new \TCPDF('P', 'mm', 'A4', true, 'ISO-8859-1');
        // Since we currently don't have a OCRB font for TCPDF, we disable this
        $fontname = \TCPDF_FONTS::addTTFfont(realpath(__DIR__ . '/../../public/fonts/ocrb10.ttf'), 'TrueTypeUnicode', '', 32);

        //$tcPdf->AddFont('OCRB10');
        $tcPdf->AddFont($fontname);
        // Disable TCPDF's default behaviour of print header and footer
        $tcPdf->setPrintHeader(false);
        $tcPdf->setPrintFooter(false);
        // Add page, don't break page automatically
        $tcPdf->AddPage();
        $tcPdf->SetAutoPageBreak(false);
        // Insert a dummy invoice text, not part of the payment slip itself
        //$tcPdf->SetFont('Helvetica', '', 9);
        $tcPdf->SetFont($fontname, '', 9);
        $tcPdf->Cell(50, 4, "Just some dummy text.");
        // Create an orange payment slip data container (value object)
        $paymentSlipData = new OrangePaymentSlipData();
        // Fill the data container with your data
        $paymentSlipData->setBankData('Seldwyla Bank', '8001 Zürich')
            ->setAccountNumber('01-145-6')
            ->setRecipientData('H. Muster AG', 'Versandhaus', 'Industriestrasse 88', '8000 Zürich')
            ->setPayerData('Rutschmann Pia', 'Marktgasse 28', '9400 Rorschach')
            ->setAmount(2830.50)
            ->setReferenceNumber('7520033455900012')
            ->setBankingCustomerId('215703');
        // Create an orange payment slip object, pass in the prepared data container
        $paymentSlip = new OrangePaymentSlip($paymentSlipData, 0, 191);
        // Get all elements (data fields with layout configuration)
       /* $elements = $paymentSlip->getAllElements();
        // Iterate through the elements (its lines and attributes)
        foreach ($elements as $elementName => $element) {
            echo "<h2>Element: " . $elementName . "</h2>";
            foreach ($element['lines'] as $lineNr => $line) {
                echo "-- Line " . $lineNr . ": " . $line . " <br>";
            }
            echo "<br>";
            foreach ($element['attributes'] as $lineNr => $line) {
                echo "-- Attribute " . $lineNr . ": " . $line . " <br>";
            }  
        }*/

        // Since we currently don't have a OCRB font for TCPDF, we set it to one we certainly have
        //$paymentSlip->setCodeLineAttr(null, null, null, null, null, 'Helvetica');
        //$paymentSlip->setCodeLineAttr(null, null, null, null, null, $fontname);
        //$paymentSlip->setReferenceNumberRightAttr(null, null, null, null, null, $fontname);
        // Create an instance of the TCPDF implementation, can be used for TCPDF, too
        $paymentSlipTcpdf = new PaymentSlipTcpdf($tcPdf);
        // "Print" the slip with its elements according to their attributes
        $paymentSlipTcpdf->createPaymentSlip($paymentSlip);

        //$pdfName = 'example_tcpdf_orange_slip.pdf';

        $file = new File($tcPdf->Output());

        //return $this->file($file);

        // rename the downloaded file
        return $this->file($file, 'custom_name.pdf');

        // display the file contents in the browser instead of downloading it
        //return $this->file('invoice_3241.pdf', 'my_invoice.pdf', ResponseHeaderBag::DISPOSITION_INLINE);

        //$pdfPath = __DIR__ . DIRECTORY_SEPARATOR . $pdfName;
        //$tcPdf->Output($pdfPath, 'F');

        //return new BinaryFileResponse($paymentSlipTcpdf->createPaymentSlip($paymentSlip));  //$this->render('base.html.twig', ['slip' => $paymentSlip ]);
    }
}