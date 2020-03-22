<?php

namespace Mautic\InstallBundle\InstallFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Mautic\CoreBundle\Doctrine\Helper\ColumnSchemaHelper;
use Mautic\CoreBundle\Doctrine\Helper\IndexSchemaHelper;
use Mautic\CoreBundle\Exception\SchemaException;
use Mautic\LeadBundle\Entity\LeadField;
use Mautic\LeadBundle\Model\FieldModel;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class LeadFieldData.
 */
class CreateLeadFieldData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * {@inheritdoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {

        $fieldGroups['lead']    = FieldModel::$coreFields;
        $fieldGroups['company'] = FieldModel::$coreCompanyFields;


        $translator   = $this->container->get('translator');
        $indexesToAdd = [];

        $fieldsData = CsvHelper::csv_to_array(__DIR__.'/lead_fields.csv');

        $order = 1;
        foreach ($fieldsData as $count => $rows) {
            $entity = new LeadField();
            $key  = $count + 1;
            $alias=''; $type = ''; $object = '';
            foreach ($rows as $col => $val) {
                if ($val != 'NULL') {
                    $setter = 'set'.ucfirst($col);
                    
                    // если alias, то создадим еще Label
                    if ($col == 'alias') {
                        $alias = $val;
                        $entity->setLabel($translator->trans('mautic.lead.field.'.$val, [], 'fixtures'));
                    }

                    if ($col == 'type')  {
                        $type = $val;
                    }

                    if ($col == 'object')  {
                        $object = $val;
                    }
                    
                    // из строки в булиан )
                    if ($val == 'Yes') $val = true;
                    if ($val == 'No') $val = false;

                    $entity->$setter($val);
                }
            }

            // поля по умолчанию    
            $entity->setProperties([]);
            $entity->setOrder($order);

            $manager->persist($entity);
            $manager->flush();

            try {
                $schema->addColumn(
                    FieldModel::getSchemaDefinition($alias, $type, $entity->getIsUniqueIdentifier())
                );
            } catch (SchemaException $e) {
                // Schema already has this custom field; likely defined as a property in the entity class itself
            }

            $indexesToAdd[$object][$alias] = $field;

            $this->addReference('leadfield-'.$alias, $entity);
            ++$order;
        }
        $schema->executeChanges();
       

        foreach ($indexesToAdd as $object => $indexes) {
            if ($object == 'company') {
                /** @var IndexSchemaHelper $indexHelper */
                $indexHelper = $this->container->get('mautic.schema.helper.factory')->getSchemaHelper('index', 'companies');
            } else {
                /** @var IndexSchemaHelper $indexHelper */
                $indexHelper = $this->container->get('mautic.schema.helper.factory')->getSchemaHelper('index', 'leads');
            }

            foreach ($indexes as $name => $field) {
                $type = (isset($field['type'])) ? $field['type'] : 'text';
                if ('textarea' != $type) {
                    $indexHelper->addIndex([$name], $name.'_search');
                }
            }
            if ($object == 'lead') {
                // Add an attribution index
                $indexHelper->addIndex(['attribution', 'attribution_date'], 'contact_attribution');
                //Add date added and country index
                $indexHelper->addIndex(['date_added', 'country'], 'date_added_country_index');
            } else {
                $indexHelper->addIndex(['companyname', 'companyemail'], 'company_filter');
                $indexHelper->addIndex(['companyname', 'companycity', 'companycountry', 'companystate'], 'company_match');
            }

            $indexHelper->executeChanges();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 4;
    }
}