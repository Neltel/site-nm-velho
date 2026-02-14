<?php
// admin/includes/footer-admin.php
?>
   
        </main>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
     <script src="../js/admin.js"></script>
    <?php
// admin/includes/footer-admin.php
?>
    <!-- Scripts Customizados -->
    <script>
        // Auto-save para algumas configurações
        let saveTimeout;
        document.querySelectorAll('input, select, textarea').forEach(element => {
            element.addEventListener('change', function() {
                clearTimeout(saveTimeout);
                saveTimeout = setTimeout(() => {
                    // Adicionar feedback visual de salvamento automático
                    const feedback = document.createElement('div');
                    feedback.className = 'alert alert-info alert-dismissible fade show position-fixed top-0 end-0 m-3';
                    feedback.innerHTML = '<i class="fas fa-save me-2"></i>Alterações salvas!';
                    document.body.appendChild(feedback);
                    setTimeout(() => feedback.remove(), 2000);
                }, 1000);
            });
        });

        // Validação de formulário
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function(e) {
                const whatsapp = document.querySelector('[name="whatsapp_empresa"]');
                if (whatsapp && !whatsapp.value.match(/^55\d{10,11}$/)) {
                    e.preventDefault();
                    alert('Formato do WhatsApp inválido! Use: 55DDDNUMERO (ex: 5517996240725)');
                    whatsapp.focus();
                    return false;
                }
            });
        });

        // Atualizar preview das cores
        document.querySelectorAll('input[type="color"]').forEach(input => {
            input.addEventListener('change', function() {
                document.documentElement.style.setProperty('--primary-color', document.querySelector('[name="config_cor_primaria"]').value);
                document.documentElement.style.setProperty('--secondary-color', document.querySelector('[name="config_cor_secundaria"]').value);
            });
        });
    </script>
</body>
</html>