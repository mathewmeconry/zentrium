<?php

namespace Zentrium\Bundle\CoreBundle\Form\Type;

use libphonenumber\PhoneNumberUtil;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Zentrium\Bundle\CoreBundle\Form\DataTransformer\PhoneNumberToStringTransformer;
use Zentrium\Bundle\CoreBundle\Templating\Helper\PhoneNumberHelper;

class PhoneNumberType extends AbstractType
{
    /**
     * @var PhoneNumberUtil
     */
    private $util;

    /**
     * @var PhoneNumberHelper
     */
    private $helper;

    /**
     * Constructor.
     *
     * @param PhoneNumberUtil   $util
     * @param PhoneNumberHelper $helper
     */
    public function __construct(PhoneNumberUtil $util, PhoneNumberHelper $helper)
    {
        $this->util = $util;
        $this->helper = $helper;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addViewTransformer(new PhoneNumberToStringTransformer($this->util, $this->helper));
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['type'] = 'tel';
    }

    public function getParent()
    {
        return TextType::class;
    }
}
