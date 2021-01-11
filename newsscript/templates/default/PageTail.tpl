

  <p class="text-center text-muted small">
   CHS Newsscript {$version}<br />
   {$queryCounter|string_format:Language::getInstance()->getString('executed_x_queries')}<br />
   {$memoryUsage|string_format:Language::getInstance()->getString('x_kib_memory_usage')}
  </p>
 </body>
</html>