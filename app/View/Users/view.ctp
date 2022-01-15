<?php $id = $record['User']['id'];
echo $this->element('menu_view', compact('id')); ?>
<div class="hr"><hr /></div>

<div class="list">
<table>
    <tr>
        <th><?php echo $fieldnames['username']; ?></th>
        <td><?php echo h($record['User']['username']); ?></td>
    </tr>
    <tr>
        <th><?php echo $fieldnames['name']; ?></th>
        <td><?php echo h($record['User']['name']); ?></td>
    </tr>
    <tr>
        <th><?php echo $fieldnames['group_id']; ?></th>
        <td><?php echo h($record['Group']['name']); ?></td>
    </tr>
    <tr>
        <th><?php echo $fieldnames['email']; ?></th>
        <td><?php echo h($record['User']['email']); ?></td>
    </tr>
    <tr>
        <th><?php echo $fieldnames['customer_organization_id']; ?></th>
        <td><?php echo h($record['CustomerOrganization']['path']); ?></td>
    </tr>
    <tr>
        <th><?php echo $fieldnames['company_name_for_mail']; ?></th>
        <td><?php echo h($record['User']['company_name_for_mail']); ?></td>
    </tr>
    <tr>
        <th><?php echo $fieldnames['person_name_for_mail']; ?></th>
        <td><?php echo h($record['User']['person_name_for_mail']); ?></td>
    </tr>
    <tr>
        <th><?php echo $fieldnames['contact_address']; ?></th>
        <td class="contact"><?php echo $this->Mds->format_contact_address($record['User']['contact_address']); ?></td>
    </tr>
    <?php if ($record['Group']['id']>3) {?>
    <tr>
        <th><?php echo $fieldnames['mps_customer_id']; ?></th>
        <td><?php foreach ($customer_organizations as $c) {
              echo $c;?><br/>
            <?php } ?>
        </td>
    </tr>
    <?php } ?>
</table>
</div><!-- .list -->
