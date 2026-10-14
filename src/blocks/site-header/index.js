import { registerSectionBlock } from '@ci-theme/editor';
import metadata from './block.json';
import Edit from './edit';
import './style.css';
import './editor.css';

registerSectionBlock( metadata, { edit: Edit } );
