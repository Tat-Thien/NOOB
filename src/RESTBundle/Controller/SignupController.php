<?php

namespace RESTBundle\Controller;

use DateTime;
use FOS\RestBundle\Controller\Annotations as REST;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * @REST\RouteResource("Signup")
 */
class SignupController extends RESTBundleController
{

    /**
     * @REST\QueryParam(name="access_token", allowBlank=false)
     * @ApiDoc(
     *  resource=true,
     *  description="Sign up a person on NOOB",
     *  input="RESTBundle\Form\SignupType",
     *  output="RESTBundle\Form\SignupType"
     * )
     */
    public function postAction(ParamFetcherInterface $paramFetcher, Request $request)
    {
        $this->checkAuthentication($paramFetcher);
        $data = new SignupData();
        $form = $this->createForm(new SignupType(), $data);
        $form->submit($request);
        if ($form->isValid()) {
            $data->sendNotificationMail();
            $person = Person::fromSignup($data);
            $em = $this->getDoctrine()->getManager();
            $em->persist($person);
            $em->flush();
            return $this->returnCreationResponse($person);
        }
        return array(
            'form' => $form
        );
    }

}
