# ANKH Tattoo - Landing v2

Landing page estática criada como nova opção visual para o site da ANKH Tattoo.

## Arquivos principais

- `index.html`: estrutura da página
- `assets/css/style.css`: layout, responsividade e visual dark premium
- `assets/js/main.js`: menu mobile, header no scroll e animações de entrada
- `assets/img/hero-anubis.svg`: arte vetorial conceitual do Anúbis usada no hero
- `assets/img/meduri-placeholder.svg`: placeholder visual para a seção do Meduri

## O que trocar depois

Para deixar o site pronto para produção, substitua os placeholders por fotos reais:

- Foto real do Meduri na seção `Sobre Meduri`
- Fotos reais das tatuagens no bloco `Trabalhos em destaque`
- Instagram correto, caso não seja `@ankhtattoo`

No HTML, os cards de portfólio usam placeholders em CSS para evitar depender de imagens provisórias ruins. Quando tiver as fotos reais, o ideal é trocar cada `<i class="work-art ..."></i>` por:

```html
<img src="assets/img/nome-da-foto.webp" alt="Descrição da tatuagem">
```

## Contato configurado

- WhatsApp: `+55 11 98016-5941`
- Endereço: `R. Rodrigo Vieira, 447 - Jardim Vila Mariana, São Paulo - SP, 04115-060`
- Equipe: apenas Meduri

## Observação

A versão foi colocada em `/v2` para não mexer no site atual. Sim, civilização básica, mas surpreendentemente útil.
