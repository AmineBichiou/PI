document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".btn-convertir").forEach(button => {
        button.addEventListener("click", function () {
            let commandeId = this.dataset.id;
            let prixTotal = parseFloat(this.dataset.prixtotal); // Assurer que c'est un nombre valide
            
            if (isNaN(prixTotal) || prixTotal <= 0) {
                console.error("❌ Montant invalide :", prixTotal);
                alert("❌ Montant invalide !");
                return;
            }

            fetch(`/api/conversion?montant=${prixTotal}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        let cellPrixTotal = document.getElementById(`prix-total-${commandeId}`);
                        if (cellPrixTotal) {
                            cellPrixTotal.innerHTML = `
                                ${prixTotal.toFixed(2)} DT <br>
                                <small class="text-muted">
                                    ≈ ${data.conversion.USD.toFixed(2)} $ | 
                                    ${data.conversion.EUR.toFixed(2)} € | 
                                    ${data.conversion.GBP.toFixed(2)} £
                                </small>
                            `;
                        }
                    } else {
                        alert("⚠️ Erreur lors de la conversion !");
                    }
                })
                .catch(error => {
                    console.error("❌ Erreur API conversion :", error);
                    alert("⚠️ Une erreur est survenue.");
                });
        });
    });
});
