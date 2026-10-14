import { registerSectionBlock } from '@ci-theme/editor';
import metadata from './block.json';
import Edit from './edit';
import './style.css';

registerSectionBlock( metadata, { edit: Edit } );
