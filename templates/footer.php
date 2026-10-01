<?php
/**
 * templates/footer.php
 * Closes the page structure opened in header.php.
 */
?>
</div><!-- /.site-wrapper -->

<footer class="site-footer">
  <p>
    &copy; <?= date('Y') ?> CMS Detector &nbsp;|&nbsp;
    Developed by:
    <a href="https://wa.me/97466489944?text=Hi%20saieed,%20I%20saw%20Your%20Developed%20Site%20%22CMS%20Detector%22" target="_blank" rel="noopener">SidMan Solution</a>
  </p>
</footer>

<style>
.site-footer {
  position: relative;
  z-index: 1;
  text-align: center;
  padding: 32px 24px;
  font-size: .8rem;
  color: var(--text-muted);
  border-top: 1px solid var(--border);
}
.site-footer a { color: var(--text-secondary); }
.site-footer a:hover { color: var(--accent); }
</style>

</body>
</html>
