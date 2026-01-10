#!/usr/bin/env python3
"""
Script para extrair TODAS as imagens PNG do DS4Windows.dll
Extrai imagens embutidas como recursos .NET

Uso: python3 extract_dll_images.py
"""

import os
import struct

def extract_png_images(dll_path, output_dir='extracted_images'):
    """Extrai todas as imagens PNG de um arquivo DLL"""
    
    # Criar diretório de saída
    if not os.path.exists(output_dir):
        os.makedirs(output_dir)
    
    print(f"Lendo {dll_path}...")
    with open(dll_path, 'rb') as f:
        data = f.read()
    
    # Assinatura PNG: 89 50 4E 47 0D 0A 1A 0A
    png_sig = b'\x89PNG\r\n\x1a\n'
    
    # Encontrar todas as imagens PNG
    pngs_found = []
    offset = 0
    
    while True:
        offset = data.find(png_sig, offset)
        if offset == -1:
            break
        
        # Encontrar o final do PNG (chunk IEND)
        iend_offset = data.find(b'IEND', offset)
        if iend_offset != -1:
            # IEND é seguido por 4 bytes CRC
            end_offset = iend_offset + 8
            png_data = data[offset:end_offset]
            pngs_found.append((offset, png_data))
        
        offset += 1
    
    print(f"\n✅ Encontradas {len(pngs_found)} imagens PNG no DLL!\n")
    
    # Salvar todas as imagens
    for i, (off, png_data) in enumerate(pngs_found, 1):
        filename = f'{output_dir}/image_{i:03d}_offset_{off:08x}.png'
        with open(filename, 'wb') as f:
            f.write(png_data)
        
        # Mostrar informação sobre cada imagem
        size_kb = len(png_data) / 1024
        print(f"  [{i:2d}] {filename}")
        print(f"       Tamanho: {size_kb:.1f} KB | Offset: 0x{off:08x}")
    
    print(f"\n✅ Todas as imagens foram extraídas para: {output_dir}/")
    print(f"\n💡 Dica: As imagens maiores (>50 KB) geralmente são fundos/logos")
    print(f"         As menores (<5 KB) são ícones da interface")
    
    return len(pngs_found)

if __name__ == '__main__':
    dll_file = 'DS4Windows.dll'
    
    if not os.path.exists(dll_file):
        print(f"❌ Erro: Arquivo '{dll_file}' não encontrado!")
        print(f"   Certifique-se de que o DS4Windows.dll está no mesmo diretório que este script.")
        exit(1)
    
    try:
        total = extract_png_images(dll_file)
        print(f"\n🎉 Extração concluída com sucesso! Total: {total} imagens")
    except Exception as e:
        print(f"❌ Erro durante a extração: {e}")
        exit(1)
