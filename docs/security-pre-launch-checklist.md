# Security Pre-Launch Checklist

This checklist must be completed **before making the repository public** to protect self-hosted runners from malicious fork PRs.

Related: [Issue #328](https://github.com/frontman-ai/frontman/issues/328)

## Critical (Must Complete Before Going Public)

### 1. Enable Fork PR Approval Requirement
**Status**: ⬜ Not Started  
**Responsibility**: Repository Admin  
**Instructions**:
1. Navigate to: `Settings → Actions → General → Fork pull request workflows`
2. Select: **"Require approval for all outside collaborators"**
3. Verify the setting is saved

**Why**: Prevents fork PRs from running on self-hosted runners until a maintainer explicitly approves.

---

### 2. Configure Production Environment Protection Rules
**Status**: ⬜ Not Started  
**Responsibility**: Repository Admin  
**Instructions**:
1. Navigate to: `Settings → Environments → production`
2. Enable **Required reviewers**:
   - Add: `@BlueHotDog`
   - Add: `@itayadler`
3. Set **Deployment branches**: **Protected branches only**
4. (Optional) Set **Wait timer**: **5 minutes**
5. Click **Save protection rules**

**Why**: Since CI runners share the same network as production, this ensures:
- Even if malicious code is merged, deployment requires manual approval
- Attackers cannot deploy from fork branches
- You get a window to cancel suspicious deployments

---

### 3. Enable Branch Protection with Code Owner Reviews
**Status**: ⬜ Not Started (requires public repo or GitHub Pro)  
**Responsibility**: Repository Admin  
**Instructions**:
1. Navigate to: `Settings → Branches → Branch protection rules → main`
2. Enable: **Require a pull request before merging**
3. Enable: **Require review from Code Owners**
4. Enable: **Require status checks to pass before merging**
5. Select required status checks: (all CI jobs)
6. Enable: **Require branches to be up to date before merging**
7. Click **Save changes**

**Why**: Ensures workflow file changes in `.github/workflows/` require review from trusted maintainers (defined in `.github/CODEOWNERS`).

**Note**: This feature requires either GitHub Pro or a public repository. Enable immediately after making the repo public.

---

## Already Completed ✅

### ✅ Restrictive Default GITHUB_TOKEN Permissions
**Completed in**: [PR #330](https://github.com/frontman-ai/frontman/pull/330)  
**Status**: ✅ Complete

Top-level `permissions: contents: read` added to:
- `.github/workflows/ci.yml`
- `.github/workflows/changelog-check.yml`

Jobs that need additional permissions escalate per-job.

---

### ✅ CODEOWNERS File
**Completed in**: [PR #330](https://github.com/frontman-ai/frontman/pull/330)  
**Status**: ✅ Complete

`.github/CODEOWNERS` created protecting:
- `.github/workflows/` (all workflow files)
- `.github/CODEOWNERS` (the file itself)
- `infra/` (infrastructure code)

Maintainers required for approval: `@BlueHotDog`, `@itayadler`

---

### ✅ Pin Third-Party Actions to Commit SHAs
**Completed in**: [PR #329](https://github.com/frontman-ai/frontman/pull/329)  
**Status**: ✅ Complete

All 69 action references across 6 workflow files pinned to immutable commit SHAs:
- `actions/checkout@de0fac2e4500dabe0009e67214ff5f5447ce83dd # v6.0.2`
- `actions/setup-node@6044e13b5dc448c55e2357c09f80417699197238 # v6.2.0`
- `actions/cache@cdf6c1fa76f9f475f3d7449005a359c84ca0f306 # v5.0.3`
- `dorny/paths-filter@...`
- `jdx/mise-action@...`
- `cloudflare/wrangler-action@...`
- `5monkeys/cobertura-action@...` (previously highest risk: `@master`)

---

### ✅ Secrets Isolated to Deploy Workflows
**Status**: ✅ Complete (verified)

**CI workflows use zero production secrets**:
- `ci.yml` - No secrets
- `changelog-check.yml` - No secrets

**Deploy workflows use `environment: production`**:
- `deploy.yml` - Uses `PROD_SERVER`, `DEPLOY_SSH_KEY_B64`, `CLOUDFLARE_API_TOKEN`, `CLOUDFLARE_ACCOUNT_ID`
- `deploy-marketing.yml` - Uses `CLOUDFLARE_API_TOKEN`, `CLOUDFLARE_ACCOUNT_ID`

Even if an attacker compromises a CI job, they cannot access production credentials.

---

## Recommended (Strong Additional Hardening)

### Switch to Ephemeral Runners
**Status**: ⬜ Not Started  
**Responsibility**: Infrastructure Team  
**Priority**: Medium

Configure self-hosted runners with the `--ephemeral` flag:
```bash
./config.sh --url https://github.com/frontman-ai/frontman --token TOKEN --ephemeral
```

**Why**: Each runner picks up exactly one job then unregisters. Prevents state leakage between jobs (e.g., a malicious job leaving a backdoor for a subsequent deploy job).

---

## Defense-in-Depth Summary

With all critical items completed, an attacker attempting to compromise production would need to:

1. ❌ **Create a malicious PR** → Blocked by fork PR approval requirement
2. ❌ **Get code owner approval** → Blocked by CODEOWNERS protection
3. ❌ **Merge to main** → Blocked by branch protection rules
4. ❌ **Access production secrets** → Blocked by environment isolation
5. ❌ **Get deployment approval** → Blocked by required reviewers
6. ❌ **Deploy from a fork branch** → Blocked by deployment branch restriction

---

## Pre-Launch Verification

Before making the repo public, verify all critical items:

```bash
# 1. Check CODEOWNERS exists
[ -f .github/CODEOWNERS ] && echo "✅ CODEOWNERS exists" || echo "❌ CODEOWNERS missing"

# 2. Check ci.yml has restricted permissions
grep -q "^permissions:" .github/workflows/ci.yml && echo "✅ ci.yml has permissions block" || echo "❌ Missing permissions"

# 3. Check actions are pinned (no @v or @master references except in comments)
if ! grep -E 'uses:.*@(v[0-9]|master|main)' .github/workflows/*.yml | grep -v '#'; then
  echo "✅ All actions pinned to SHAs"
else
  echo "❌ Some actions not pinned"
fi

# 4. Check deploy workflows use environment protection
grep -q "environment: production" .github/workflows/deploy*.yml && echo "✅ Deploy workflows use environment" || echo "❌ Missing environment"
```

Then manually verify via GitHub UI:
- [ ] Fork PR approval is enabled
- [ ] Production environment has required reviewers
- [ ] Production environment has deployment branch restriction

---

## Post-Launch Tasks

After making the repo public:

1. **Enable branch protection with code owner reviews** (requires public repo)
2. **Monitor first few fork PRs closely** to verify approval workflow works
3. **Set up alerting** for deployment approvals (GitHub webhook → Slack/email)

---

## References

- [GitHub: Security hardening for self-hosted runners](https://docs.github.com/en/actions/security-for-github-actions/security-guides/security-hardening-for-github-actions#hardening-for-self-hosted-runners)
- [GitHub: Approving workflow runs from public forks](https://docs.github.com/en/actions/managing-workflow-runs/approving-workflow-runs-from-public-forks)
- [GitHub: Using environments for deployment](https://docs.github.com/en/actions/deployment/targeting-different-environments/using-environments-for-deployment)
