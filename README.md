# Invisible-reCaptcha-for-WordPress-GF-Part-1
This is not mine but their current version does not work so this is my fix.

1. Get your keys for Invisible Recaptcha from https://www.google.com/url?q=https://g.co/recaptcha/invisiblesignup&sa=D&ust=1490201241697000&usg=AFQjCNFDZFudeV6RGUFLW2bGaABpsSNUDA

2. Network Activate it. And under Gravity Froms Check the box for protection. 
  ** Important you need to install part 2 so you can enable it on a page by page basis.
  
3. Install Part 2: https://github.com/CountryNerd/Invisible-reCaptcha-for-WordPress-GF-Part-2

Note on multiPage form it will only fire on the last page. And if the user has already verifyed from google Invisble recaptcha it will not ask them again for the next multi page for. Not the best solution but Gravity froms fires an onsubmission for everypage "Next" button. Which invisible recaptcha plays off of.
