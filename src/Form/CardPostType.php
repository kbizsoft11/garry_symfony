<?php
    namespace App\Form;

    use App\Entity\PostCard;
    use Symfony\Component\Form\AbstractType;
	use Symfony\Component\Form\FormBuilderInterface;
	use Symfony\Component\Form\FormEvent;
	use Symfony\Component\Form\FormEvents;

    use Symfony\Component\Form\Extension\Core\Type\SubmitType;
    use Symfony\Component\Form\Extension\Core\Type\TextType;
	use Symfony\Component\OptionsResolver\OptionsResolver;
	use Symfony\Component\String\Slugger\SluggerInterface;
    use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
    use Symfony\Component\Form\Extension\Core\Type\HiddenType;
	use Symfony\Component\Form\Extension\Core\Type\FileType;

    class CardPostType extends AbstractType
    {
        public function buildForm(FormBuilderInterface $builder, array $options)
        {
            $builder->add('cardTitle', TextType::class,[
                'label' => 'Card Title',
            ])
            ->add('post_id', HiddenType::class,[
                'label' => 'Post Id',
            ])	
			->add('cardImage', FileType::class,[
                'label' => 'Card Image',
            ])				
			->add('paragraph', TextType::class,[
                'label' => 'Paragraph 1',
            ])
            ->add('template', ChoiceType::class, [
                'choices'  => [
                    'Template1' => 'Template1',
                    'Template2' => 'Template2',
                    'Template3' => 'Template3',
                ],
            ]);
        } 
		
		 /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([ 
            'data_class' => PostCard::class,
        ]);
    }
 
    }