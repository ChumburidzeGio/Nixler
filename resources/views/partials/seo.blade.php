<script type="application/ld+json">
{
  "@context": "http://schema.org",
  "@type": "Organization",
  "url": "{{ config('app.url') }}",
  "logo": "{{ url('img/logo.png') }}",
  "name": "{{ config('app.name') }}",
  "sameAs": [
  	"facebook.com/nixler.georgia",
    "https://www.instagram.com/phoenix.clothings/",
    "https://www.linkedin.com/company/17973812/"
  ],
  "contactPoint": [{
    "@type": "ContactPoint",
    "telephone": "{{ config('contact.phone.GE') }}",
    "contactType": "customer service"
  }],
  "potentialAction": {
    "@type": "SearchAction",
    "target": "{{ url('?query={search_term_string}') }}",
    "query-input": "required name=search_term_string"
  }
}
</script>