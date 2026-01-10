# Guia: Como Editar e Substituir Imagens no DS4Windows.dll

## 📋 Visão Geral

Este guia explica como **extrair**, **editar** e **substituir** imagens no DS4Windows.dll usando ferramentas gratuitas.

---

## 🔧 Ferramentas Necessárias

1. **Python 3** (para extrair imagens) - [Download](https://www.python.org/downloads/)
2. **dnSpy** (para editar DLL) - [Download](https://github.com/dnSpy/dnSpy/releases)
3. **Editor de imagem** (GIMP, Photoshop, Paint.NET, etc.)

---

## 📦 PASSO 1: Extrair Imagens

### Usando o Script Python

1. **Coloque o script** `extract_dll_images.py` no mesmo diretório que `DS4Windows.dll`

2. **Execute o script:**
   ```bash
   python3 extract_dll_images.py
   ```

3. **Resultado:** 
   - Todas as imagens serão extraídas para a pasta `extracted_images/`
   - Nomes dos arquivos incluem offset (localização) no DLL

### Identificar Qual Imagem Editar

As imagens extraídas terão nomes como:
- `image_001_offset_0019f54a.png` - Primeira imagem encontrada
- `image_042_offset_001e03d9.png` - Imagem maior (provavelmente logo/fundo)

**Dica:** Imagens grandes (>50 KB) geralmente são fundos ou logos.

---

## 🎨 PASSO 2: Editar as Imagens

1. **Abra a imagem** no seu editor favorito
2. **Faça as modificações** (mudar cores, adicionar texto, etc.)
3. **Salve como PNG** com o mesmo nome

**Importante:** 
- Mantenha as **mesmas dimensões** (largura x altura)
- Salve no formato **PNG**

---

## 🔄 PASSO 3: Substituir no DLL com dnSpy

### 3.1 Abrir o DLL no dnSpy

1. **Abra dnSpy**
2. **File → Open** → Selecione `DS4Windows.dll`
3. **Expanda a árvore** à esquerda até encontrar recursos

### 3.2 Localizar os Recursos

Na árvore à esquerda, procure por:
```
DS4Windows
  └─ DS4Windows.g.resources
  └─ DS4WinWPF.Properties.Resources.resources
```

### 3.3 Substituir a Imagem

1. **Clique duas vezes** no arquivo `.resources`
2. **Encontre a imagem** que deseja substituir
3. **Clique direito** na imagem → **Replace Resource**
4. **Selecione** sua imagem PNG editada
5. **Confirme** a substituição

### 3.4 Salvar o DLL Modificado

1. **File → Save Module**
2. **Escolha um nome** (ex: `DS4Windows_Modified.dll`)
3. **Salve** em um local seguro

**⚠️ IMPORTANTE:** Faça backup do DLL original antes de substituir!

---

## 📝 PASSO 4: Modificações Adicionais (Opcional)

### Mudar @DS4Prada para DS4Mega

1. No dnSpy, pressione **Ctrl+Shift+K**
2. Digite: `DS4Prada`
3. **Encontre** a string no código
4. **Clique direito** → **Edit Class**
5. **Mude** `"@DS4Prada"` para `"@DS4Mega"`
6. **Compile** (botão Compile)

### Mudar Idioma Padrão para Português

1. Procure por classes com `Language` ou `Culture`
2. Encontre o método que define o idioma padrão
3. Mude de `"en-US"` para `"pt-BR"`

### Mudar Tema Padrão para Light (Claro)

1. Procure por `Theme` ou `AppTheme`
2. Encontre onde define o tema inicial
3. Mude de `"Dark"` para `"Light"` ou `false` para `true`

---

## ✅ PASSO 5: Testar

1. **Renomeie** o DLL original: `DS4Windows.dll.backup`
2. **Renomeie** o DLL modificado para: `DS4Windows.dll`
3. **Execute** o DS4Windows.exe
4. **Verifique** se as imagens foram alteradas

Se algo der errado:
- Restaure o backup: `DS4Windows.dll.backup` → `DS4Windows.dll`

---

## 🎯 Dicas Importantes

### ✅ Faça Sempre
- ✅ Backup do DLL original
- ✅ Mantenha dimensões originais das imagens
- ✅ Use formato PNG
- ✅ Teste antes de substituir o arquivo original

### ❌ Evite
- ❌ Mudar dimensões das imagens drasticamente
- ❌ Usar formatos diferentes de PNG
- ❌ Editar sem fazer backup
- ❌ Modificar múltiplas coisas de uma vez (teste incrementalmente)

---

## 🆘 Solução de Problemas

### Problema: dnSpy não mostra recursos

**Solução:** Use Resource Hacker em vez de dnSpy
- Download: http://www.angusj.com/resourcehacker/
- Abra DLL → Expanda "Bitmap" ou "PNG"

### Problema: DLL modificado não funciona

**Soluções:**
1. Certifique-se de que compilou corretamente (sem erros)
2. Verifique se manteve as dimensões originais das imagens
3. Restaure o backup e tente novamente com menos modificações

### Problema: Imagens não aparecem alteradas

**Soluções:**
1. Limpe o cache do Windows (reinicie o PC)
2. Verifique se substituiu a imagem correta
3. Confirme que salvou o módulo no dnSpy

---

## 📚 Recursos Adicionais

- **dnSpy Documentação:** https://github.com/dnSpy/dnSpy/wiki
- **Tutorial .NET Decompiling:** https://www.youtube.com/results?search_query=dnspy+tutorial
- **Edição de PNG:** https://www.gimp.org/tutorials/

---

## 📧 Suporte

Se tiver dúvidas, consulte:
1. Este guia
2. Documentação do dnSpy
3. Comunidade DS4Windows

**Boa sorte com suas modificações! 🎮**
