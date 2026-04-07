# Frontman Server
# Copyright (C) 2025 Frontman AI
#
# Licensed under the AGPL-3.0 — see LICENSE for details.
# Additional terms apply — see AI-SUPPLEMENTARY-TERMS.md

defmodule FrontmanServer.Observability.Events do
  @moduledoc """
  Telemetry event name definitions for FrontmanServer.

  Single source of truth for event names used by TelemetryEvents (emitter)
  and OtelHandler (consumer).

  Note: Agent execution events (loop, step, llm, tool, child) are defined
  in SwarmAi.Telemetry.Events and handled by SwarmOtelHandler.
  """

  @prefix [:frontman]

  # Task
  def task_start, do: @prefix ++ [:task, :start]
  def task_stop, do: @prefix ++ [:task, :stop]
end
