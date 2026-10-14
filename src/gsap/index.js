/**
 * Shared GSAP (handle "ci-theme-gsap", window.ciThemeGsap): a single copy for the section
 * animations and the header. Other scripts import 'gsap' and 'gsap/<Plugin>' and webpack
 * resolves them here (see webpack.config.js). This file imports from gsap/dist so it does not
 * point to itself.
 */

import { gsap } from 'gsap/dist/gsap';
import { DrawSVGPlugin } from 'gsap/dist/DrawSVGPlugin';
import { ScrollTrigger } from 'gsap/dist/ScrollTrigger';
import { SplitText } from 'gsap/dist/SplitText';

gsap.registerPlugin( ScrollTrigger, SplitText, DrawSVGPlugin );

window.ciThemeGsap = { gsap, DrawSVGPlugin, ScrollTrigger, SplitText };
