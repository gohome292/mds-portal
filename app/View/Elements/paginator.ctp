<div class="paginator horizon">
<ul>
<li><?php echo $this->Paginator->counter(array(
    'format' => '%start% - %end% / %count%',
)); ?></li>
<li><?php echo $this->Paginator->first('<<先頭'); ?></li>
<li><?php if ($this->Paginator->hasPrev()) echo $this->paginator->prev('<前'); ?></li>
<li><?php echo $this->Paginator->numbers(); ?></li>
<li><?php if ($this->Paginator->hasNext()) echo $this->paginator->next('次>'); ?></li>
<li><?php echo $this->Paginator->last('末尾>>'); ?></li>
</ul>
</div><!-- .paginator -->
