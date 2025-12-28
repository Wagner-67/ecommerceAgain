<?php

namespace App\MessageHandler;

use App\Entity\Orders;
use App\Message\GenerateOrderPdfMessage;
use Doctrine\ORM\EntityManagerInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Twig\Environment;

#[AsMessageHandler]
class GenerateOrderPdfHandler
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private Environment $twig,
    ) {}

    public function __invoke(GenerateOrderPdfMessage $message): void
    {
        $order = $this->entityManager->getRepository(Orders::class)->find($message->orderId);

        if (!$order) {
            return;
        }

        // Assuming DeliveryAddress and BillingAddress are arrays, but perhaps they should be linked to Address entity.
        // For now, use the arrays.

        $html = $this->twig->render('order/pdf.html.twig', [
            'order' => $order,
            'orderItems' => $order->getOrderItems(),
            'deliveryAddress' => $order->getDeliveryAddress(),
            'billingAddress' => $order->getBillingAddress(),
        ]);

        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $output = $dompdf->output();

        // Save to file or send email, etc.
        // For example, save to var/pdfs/
        $pdfPath = __DIR__ . '/../../var/pdfs/order_' . $order->getId() . '.pdf';
        file_put_contents($pdfPath, $output);

        // Optionally, send email with PDF
    }
}