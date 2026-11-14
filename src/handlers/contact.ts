import { getConfig } from "../config/config";
import { send } from "../email/email";
import type { Request, Response } from "express";

export const handleContact = async (req: Request, res: Response) => {
  const inputs = {
    firstname: req.body.firstname,
    lastname: req.body.lastname,
    from: req.body.email,
    message: req.body.message,
  };

  const missing = Object.entries(inputs)
    .filter(([, v]) => v === undefined)
    .map(([k]) => k);

  if (missing.length !== 0) {
    res
      .status(400)
      .send({ error: `Required values ${missing.join(", ")} missing ` });
    return;
  }

  const success = await send({
    from: inputs.from,
    to: getConfig().email.recipient,
    subject: "New contact request",
    message: req.body.message,
    res,
  });

  if (success) {
    res.send({ msg: "Success" });
  } else {
    res.status(500).send({ msg: "Whoops" });
  }
};
