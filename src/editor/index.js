/**
 * Shared editor library (handle "ci-theme-editor", window.ciThemeEditor).
 * Blocks import it as '@ci-theme/editor' (see webpack.config.js).
 */

import './editor.css';

import { registerSectionBlock } from './register';
import { SectionPreview } from './section-preview';
import { SectionStyle } from './section-style';
import { PaletteColorControl } from './palette-color';
import { Repeater } from './repeater';
import { ButtonsControl, LinkField } from './links';
import { IntroPanel, ItemsControl, TextField } from './fields';
import { PostsPicker, QueryControls } from './query';
import { ImageField } from './media';

window.ciThemeEditor = {
	registerSectionBlock,
	SectionPreview,
	SectionStyle,
	PaletteColorControl,
	Repeater,
	ButtonsControl,
	LinkField,
	IntroPanel,
	ItemsControl,
	TextField,
	QueryControls,
	ImageField,
	PostsPicker,
};
