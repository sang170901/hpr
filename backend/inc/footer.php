		</div> <!-- .container -->
		<footer class="admin-footer">VNMaterial Admin • Prototype</footer>
	</div> <!-- .main -->
</div> <!-- .app -->
<script>
document.getElementById('btn-toggle-sidebar')?.addEventListener('click', function(){
	const sb = document.querySelector('.sidebar');
	if (!sb) return;
	sb.style.display = (sb.style.display === 'none') ? 'block' : 'none';
});
</script>
</body>
</html>
