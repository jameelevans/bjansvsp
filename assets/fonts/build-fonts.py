"""Compress the retained variable TTFs and create the Latin faces used by the theme."""
from pathlib import Path
from fontTools import subset
from fontTools.ttLib import TTFont

ROOT = Path(__file__).resolve().parent
FONTS = {
    'Roboto-Variable': 'Roboto-VariableFont_wdth,wght.ttf',
    'Roboto-Italic-Variable': 'Roboto-Italic-VariableFont_wdth,wght.ttf',
    'OpenSans-Variable': 'OpenSans-VariableFont_wdth,wght.ttf',
    'OpenSans-Italic-Variable': 'OpenSans-Italic-VariableFont_wdth,wght.ttf',
}
LATIN = (list(range(0x100)) + [0x131, 0x152, 0x153, 0x2BB, 0x2BC, 0x2C6, 0x2DA, 0x2DC]
         + list(range(0x2000, 0x2070)) + [0x20AC, 0x2122, 0x2191, 0x2193, 0x2212, 0xFEFF, 0xFFFD])
for name, source in FONTS.items():
    font = TTFont(ROOT / source)
    font.flavor = 'woff2'
    font.save(ROOT / (name + '.woff2'))
    options = subset.Options()
    options.flavor = 'woff2'
    options.layout_features = ['*']
    options.name_IDs = [0, 1, 2, 3, 4, 5, 6, 13, 14]
    options.name_languages = [0x409]
    font = subset.load_font(str(ROOT / (name + '.woff2')), options)
    subsetter = subset.Subsetter(options=options)
    subsetter.populate(unicodes=LATIN)
    subsetter.subset(font)
    subset.save_font(font, str(ROOT / (name + '-Latin.woff2')), options)
