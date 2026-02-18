// Thin wrapper to provide `export default` for `astro add` CLI compatibility.
// ReScript doesn't support default exports natively.
// NOTE: Using import + export default (not `export { x as default }`) because
// tsup strips the re-export form during bundling.
import { frontmanIntegration } from './src/FrontmanAstro.res.mjs';
export default frontmanIntegration;
export { frontmanIntegration };
