<?php

namespace AppBundle\Form;

use Doctrine\ORM\EntityRepository;
use FM\ElfinderBundle\Form\Type\ElFinderType;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title',TextType::class,array(
                'label'=>'Заглавие'
            ))
            ->add('path',FileType::class,array(
                'label'=>'Добави картинка','required'=>false
            ))
            ->add('content',CKEditorType::class,array(
                'label'=>'Съдържание',
                'config' => array(
                    'filebrowserBrowseRoute' => 'elfinder',
                    'filebrowserBrowseRouteParameters' => array(
                        'instance' => 'default',
                        'homeFolder' => ''
                    ))
            ))
            ->add('category',EntityType::class,array(
                'label'=>'Категории',
                'class'=>'AppBundle\Entity\Category',
                'query_builder'=>function(EntityRepository $er){
                    return $er->createQueryBuilder('c')
                        ->orderBy('c.name','ASC');
                },
                'choice_label'=>'name',
            ))
            ->add('writer',EntityType::class,array(
                'label'=>'Автор',
                'class'=>'AppBundle\Entity\Author',
                'query_builder'=>function(EntityRepository $er){
                    return $er->createQueryBuilder('c')
                        ->orderBy('c.name','ASC');
                },
                'choice_label'=>'name',
            ))
            ->add('enabled',CheckboxType::class,array(
                'label'=>'Разреши публикация',
                'required'=>false
            ))
            ->add('save',SubmitType::class,array('label'=>'Запис'));
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Article'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'appbundle_article';
    }


}
