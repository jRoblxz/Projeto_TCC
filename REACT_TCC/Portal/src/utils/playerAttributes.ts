export const getAttributesByPosition = (posicao: string) => {
  const pos = posicao?.toLowerCase() || "";

  if (pos.includes("goleiro")) {
    return [
      { key: "reflexo", label: "Reflexos" },
      { key: "saida_goleiro", label: "Saída de Bola" },
      { key: "jogo_aereo", label: "Jogo Aéreo" },
      { key: "um_contra_um", label: "1 vs 1" },
      { key: "posicionamento", label: "Posicionamento" },
      { key: "fisico", label: "Físico/Força" },
    ];
  }
  if (pos.includes("zagueiro")) {
    return [
      { key: "marcacao", label: "Marcação" },
      { key: "desarme", label: "Desarme" },
      { key: "cabeceio", label: "Cabeceio" },
      { key: "posicionamento", label: "Posicionamento" },
      { key: "fisico", label: "Físico/Força" },
      { key: "velocidade", label: "Velocidade" },
    ];
  }
  if (pos.includes("lateral")) {
    return [
      { key: "velocidade", label: "Velocidade" },
      { key: "cruzamento", label: "Cruzamento" },
      { key: "marcacao", label: "Marcação" },
      { key: "condicionamento", label: "Condic. Físico" },
      { key: "tecnica", label: "Técnica" },
      { key: "posicionamento", label: "Posicionamento" },
    ];
  }
  if (pos.includes("volante")) {
    return [
      { key: "marcacao", label: "Marcação" },
      { key: "passe", label: "Passe" },
      { key: "fisico", label: "Físico/Força" },
      { key: "posicionamento", label: "Posicionamento" },
      { key: "tecnica", label: "Técnica" },
      { key: "condicionamento", label: "Condic. Físico" },
    ];
  }
  if (pos.includes("meia")) {
    return [
      { key: "passe", label: "Passe" },
      { key: "visao_jogo", label: "Visão de Jogo" },
      { key: "tecnica", label: "Técnica" },
      { key: "finalizacao", label: "Finalização" },
      { key: "velocidade", label: "Velocidade" },
      { key: "condicionamento", label: "Condic. Físico" },
    ];
  }

  // Padrão (Atacante ou não definido)
  return [
    { key: "finalizacao", label: "Finalização" },
    { key: "velocidade", label: "Velocidade" },
    { key: "tecnica", label: "Técnica" },
    { key: "posicionamento", label: "Posicionamento" },
    { key: "cabeceio", label: "Cabeceio" },
    { key: "condicionamento", label: "Condic. Físico" },
  ];
};
