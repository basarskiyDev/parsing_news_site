<?php

namespace App\Datatable\Type;

use App\Entity\Post;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\DateTimeColumn;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\Column\TwigColumn;
use Omines\DataTablesBundle\DataTable;
use Omines\DataTablesBundle\DataTableTypeInterface;

class PostType implements DataTableTypeInterface
{


    public function configure(DataTable $dataTable, array $options)
    {
        $dataTable->setName('postTable')
            ->add('id', TextColumn::class)
            ->add('header', TextColumn::class)
            ->add('description', TextColumn::class)
            ->add('image', TwigColumn::class, ['template' => 'datatable/image.html.twig'])
            ->add('addingDate', DateTimeColumn::class, ['format' => 'Y-m-d'])
            ->add('lastUpdateDate', DateTimeColumn::class, ['format' => 'Y-m-d H:i:s'])
            ->add('action', TwigColumn::class, ['template' => 'datatable/action.html.twig'] )
            ->createAdapter(ORMAdapter::class, [
                'entity' => Post::class
            ])
            ->addOrderBy(0, 'desc');
    }
}