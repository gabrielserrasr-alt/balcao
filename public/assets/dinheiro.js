// Máscara de banco: o campo só aceita números e preenche da direita para a esquerda, como no Pix.
// 1 → 0,01 · 1250 → 12,50 · 125000 → 1.250,00. O cliente nunca digita ponto nem vírgula.
// Sem JavaScript o campo continua funcionando: o servidor também entende "1.250,00".

const MAXIMO_DE_DIGITOS = 10; // 99.999.999,99, o mesmo limite do servidor

function formatarCentavos(texto) {
  const digitos = texto.replace(/\D/g, '').replace(/^0+/, '').slice(0, MAXIMO_DE_DIGITOS);
  if (digitos === '') {
    return '';
  }
  const completo = digitos.padStart(3, '0');
  const reais = completo.slice(0, -2).replace(/\B(?=(\d{3})+(?!\d))/g, '.');
  return reais + ',' + completo.slice(-2);
}

document.querySelectorAll('input[data-dinheiro]').forEach((campo) => {
  campo.value = formatarCentavos(campo.value);
  campo.addEventListener('input', () => {
    campo.value = formatarCentavos(campo.value);
    // O número cresce pela direita, então o cursor fica sempre no fim
    campo.setSelectionRange(campo.value.length, campo.value.length);
  });
});
