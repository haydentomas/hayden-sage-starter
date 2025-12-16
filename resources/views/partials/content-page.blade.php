<div class="prose prose-lg max-w-none
            prose-headings:text-[var(--color-headings)]
            prose-a:text-[var(--color-primary)] prose-a:no-underline hover:prose-a:underline
            prose-strong:text-[var(--color-body)]
            prose-blockquote:border-l-[var(--color-primary)]
            prose-hr:border-slate-200">
  @php(the_content())
</div>

@if ($pagination)
  <nav class="page-nav" aria-label="Page">
    {!! $pagination !!}
  </nav>
@endif
