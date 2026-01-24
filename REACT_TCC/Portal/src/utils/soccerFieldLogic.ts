export const getFieldCoordinates = (positionName: string) => {
  if (!positionName) return null;
  const pos = positionName.toLowerCase().trim();

  // Mapeamento exato baseado no seu script original
  const exactPositions: Record<string, { top: number; left: number }> = {
    'goleiro': { top: 85, left: 50 },
    'zagueiro direito': { top: 75, left: 70 },
    'zagueiro esquerdo': { top: 75, left: 30 },
    'zagueiro central': { top: 75, left: 50 },
    'lateral direito': { top: 60, left: 85 },
    'lateral esquerdo': { top: 60, left: 15 },
    'volante': { top: 60, left: 50 },
    'meio-central': { top: 45, left: 50 },
    'meia': { top: 45, left: 50 },
    'ponta direita': { top: 25, left: 80 },
    'ponta esquerda': { top: 25, left: 20 },
    'atacante': { top: 15, left: 50 },
    'centroavante': { top: 15, left: 50 } 
  };

  if (exactPositions[pos]) return exactPositions[pos];

  // Lógica de "contém" (fallback)
  if (pos.includes('goleiro')) return exactPositions['goleiro'];
  if (pos.includes('zagueiro')) {
      if (pos.includes('direito')) return exactPositions['zagueiro direito'];
      if (pos.includes('esquerdo')) return exactPositions['zagueiro esquerdo'];
      return exactPositions['zagueiro central'];
  }
  if (pos.includes('lateral')) {
      if (pos.includes('direito')) return exactPositions['lateral direito'];
      if (pos.includes('esquerdo')) return exactPositions['lateral esquerdo'];
  }
  if (pos.includes('volante')) return exactPositions['volante'];
  if (pos.includes('meia') || pos.includes('meio')) return exactPositions['meia'];
  if (pos.includes('ponta')) {
      if (pos.includes('direita')) return exactPositions['ponta direita'];
      if (pos.includes('esquerda')) return exactPositions['ponta esquerda'];
  }
  if (pos.includes('atacante')) return exactPositions['atacante'];

  return null;
};