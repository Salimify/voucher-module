<?php

namespace App\Controller;

use App\Entity\Voucher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;

class VoucherController extends AbstractController
{

    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        return new Response(
            '<html><body><h1>Voucher Module</h1></body></html>'
        );
    }

    /**
     * @Route("/vouchers", name="voucher")
     */
    public function getVouchers(SerializerInterface  $serializer)
    {
        $repository = $this->getDoctrine()->getRepository(Voucher::class);
        $vouchers = $repository->findAll();
        $data = $serializer->serialize($vouchers, JsonEncoder::FORMAT);
        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }
}
